<?php
 class t3lib_TCEmain_CommandMap {
	const SCOPE_WorkspacesSwap = 'SCOPE_WorkspacesSwap';
	const SCOPE_WorkspacesSetStage = 'SCOPE_WorkspacesSetStage';

	const KEY_ScopeErrorMessage = 'KEY_ScopeErrorMessage';
	const KEY_ScopeErrorCode = 'KEY_ScopeErrorCode';
	const KEY_GetElementPropertiesCallback = 'KEY_GetElementPropertiesCallback';
	const KEY_GetCommonPropertiesCallback = 'KEY_GetCommonPropertiesCallback';

	/**
	 * @var t3lib_TCEmain
	 */
	protected $parent;

	/**
	 * @var array
	 */
	protected $commandMap = array();

	/**
	 * @var string
	 */
	protected $workspacesSwapMode;

	/**
	 * @var string
	 */
	protected $workspacesChangeStageMode;

	/**
	 * @var boolean
	 */
	protected $workspacesConsiderReferences;

	/**
	 * @var array
	 */
	protected $scopes;

	/**
	 * Creates this object.
	 *
	 * @param t3lib_TCEmain $parent
	 * @param array $commandMap
 *
	 */
	public function __construct(t3lib_TCEmain $parent, array $commandMap) {
		$this->setParent($parent);
		$this->set($commandMap);

		$this->workspacesSwapMode = (string)$parent->BE_USER->getTSConfigVal('options.workspaces.swapMode');
		$this->workspacesChangeStageMode = (string)$parent->BE_USER->getTSConfigVal('options.workspaces.changeStageMode');
		$this->workspacesConsiderReferences = (bool)$parent->BE_USER->getTSConfigVal('options.workspaces.considerReferences');

		$this->constructScopes();
	}

	/**
	 * @return array
	 */
	public function get() {
		return $this->commandMap;
	}

	/**
	 * @param array $commandMap
	 * @return t3lib_TCEmain_CommandMap
	 */
	public function set(array $commandMap) {
		$this->commandMap = $commandMap;
		return $this;
	}

	/**
	 * @return t3lib_TCEmain
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param t3lib_TCEmain $parent
	 * @return t3lib_TCEmain_CommandMap
	 */
	public function setParent(t3lib_TCEmain $parent) {
		$this->parent = $parent;
		return $this;
	}

	/**
	 * @return t3lib_TCEmain_CommandMap
	 */
	public function process() {
		$this->resolveWorkspacesSwapDependencies();
		$this->resolveWorkspacesSetStageDependencies();

		return $this;
	}

	/**
	 * Resolves workspaces related dependencies of the command map ($this->cmdmap).
	 * Workspaces records that have children or (relative) parents which are versionized
	 * but not published with this request, are removed from the command map. Otherwise
	 * this would produce hanging record sets and lost references.
	 *
	 * @return void
	 */
	protected function resolveWorkspacesSwapDependencies() {
		$dependency = $this->getDependencyUtility();

		foreach ($this->commandMap as $table => $liveIdCollection) {
			foreach ($liveIdCollection as $liveId => $commandCollection) {
				foreach ($commandCollection as $command => $properties) {
					if ($command === 'version' && isset($properties['action']) && $properties['action'] === 'swap') {
						if (isset($properties['swapWith']) && t3lib_div::testInt($properties['swapWith'])) {
							$dependency->addElement(
								$table, $properties['swapWith'], array('liveId' => $liveId, 'properties' => $properties)
							);
						}
					}
				}
			}
		}

		$this->applyWorkspacesDependencies($dependency, self::SCOPE_WorkspacesSwap);
	}

	/**
	 * Resolves workspaces related dependencies of the command map ($this->cmdmap).
	 * Workspaces records that have children or (relative) parents which are versionized
	 * but not published with this request, are removed from the command map. Otherwise
	 * this would produce hanging record sets and lost references.
	 *
	 * @return void
	 */
	protected function resolveWorkspacesSetStageDependencies() {

	}

	protected function applyWorkspacesDependencies(t3lib_utility_Dependency $dependency, $scope) {
		$elementsToBeVersionized = $this->transformDependentElementsToUseLiveId(
			$dependency->getElements()
		);

		$outerMostParents = $dependency->getOuterMostParents();
		/** @var $outerMostParent t3lib_utility_Dependency_Element */
		foreach ($outerMostParents as $outerMostParent) {
			$dependentElements = $this->transformDependentElementsToUseLiveId(
				$dependency->getNestedElements($outerMostParent)
			);

			$intersectingElements = array_intersect_key($dependentElements, $elementsToBeVersionized);

			if (count($intersectingElements) > 0) {
				// If at least one element intersects but not all, throw away all elements of the depdendent structure:
				if (count($intersectingElements) !== count($dependentElements) && $this->workspacesConsiderReferences === FALSE) {
					$this->purgeWithErrorMessage($intersectingElements, $scope);
				// If everything is fine or references shall be considered automatically:
				} else {
					$this->update(current($intersectingElements), $dependentElements, $scope);
				}
			}
		}
	}


	protected function purgeWithErrorMessage(array $elements, $scope) {
		/** @var $dependentElement t3lib_utility_Dependency_Element */
		foreach ($elements as $element) {
			$table = $element->getTable();
			$liveId = $element->getDataValue('liveId');
			unset($this->commandMap[$table][$liveId]['version']);

			$this->getParent()->log(
				$table, $liveId,
				5, 0, 1,
				$this->getScopeData($scope, self::KEY_ScopeErrorMessage),
				$this->getScopeData($scope, self::KEY_ScopeErrorCode),
				array(
					t3lib_BEfunc::getRecordTitle($table, t3lib_BEfunc::getRecord($table, $liveId)),
					$table, $liveId
				)
			);
		}
	}

	protected function update(t3lib_utility_Dependency_Element $intersectingElement, array $elements, $scope) {
		$orderedCommandMap = array();

		$commonProperties = call_user_func_array(
			array($this, $this->getScopeData($scope, self::KEY_GetCommonPropertiesCallback)),
			array($intersectingElement)
		);

		/** @var $dependentElement t3lib_utility_Dependency_Element */
		foreach ($elements as $element) {
			$table = $element->getTable();
			$liveId = $element->getDataValue('liveId');
			unset($this->commandMap[$table][$liveId]['version']);

			$orderedCommandMap[$table][$liveId]['version'] = array_merge(
				$commonProperties,
				call_user_func_array(
					array($this, $this->getScopeData($scope, self::KEY_GetElementPropertiesCallback)),
					array($element)
				)
			);
		}

		// Ensure that ordered command map is on top of the command map:
		$this->commandMap = t3lib_div::array_merge_recursive_overrule($orderedCommandMap, $this->commandMap);
	}

	protected function getElementSwapPropertiesCallback(t3lib_utility_Dependency_Element $element) {
		return array(
			'swapWith' => $element->getId(),
		);
	}

	protected function getCommonSwapPropertiesCallback(t3lib_utility_Dependency_Element $element) {
		$commonSwapProperties = array();

		$elementProperties = $element->getDataValue('properties');
		if (isset($elementProperties['swapIntoWS'])) {
			$commonSwapProperties['swapIntoWS'] = $elementProperties['swapIntoWS'];
		}

		return $commonSwapProperties;
	}

	protected function getElementSetStagePropertiesCallback(t3lib_utility_Dependency_Element $element) {

	}

	protected function getCommonSetStagePropertiesCallback(t3lib_utility_Dependency_Element $element) {
		
	}


	/**
	 * @return t3lib_utility_Dependency
	 */
	protected function getDependencyUtility() {
		$createNewDependentElementCallback = t3lib_div::makeInstance(
			't3lib_utility_Dependency_Callback',
			$this, 'createNewDependentElementCallback'
		);
		$createNewDependentElementChildReferenceCallback = t3lib_div::makeInstance(
			't3lib_utility_Dependency_Callback',
			$this, 'createNewDependentElementChildReferenceCallback'
		);
		$createNewDependentElementParentReferenceCallback = t3lib_div::makeInstance(
			't3lib_utility_Dependency_Callback',
			$this, 'createNewDependentElementParentReferenceCallback'
		);

		/** @var $dependency t3lib_utility_Dependency */
		$dependency = t3lib_div::makeInstance('t3lib_utility_Dependency')
			->setOuterMostParentsRequireReferences(TRUE)
			->setEventCallback(t3lib_utility_Dependency_Element::EVENT_Construct, $createNewDependentElementCallback)
			->setEventCallback(t3lib_utility_Dependency_Element::EVENT_CreateChildReference, $createNewDependentElementChildReferenceCallback)
			->setEventCallback(t3lib_utility_Dependency_Element::EVENT_CreateParentReference, $createNewDependentElementParentReferenceCallback);

		return $dependency;
	}

	public function createNewDependentElementChildReferenceCallback(array $callerArguments, array $targetArgument, t3lib_utility_Dependency_Element $caller, $eventName) {
		/** @var $reference t3lib_utility_Dependency_Reference */
		$reference = $callerArguments['reference'];

		$fieldCOnfiguration = t3lib_BEfunc::getTcaFieldConfiguration($caller->getTable(), $reference->getField());

		if (!$fieldCOnfiguration || !t3lib_div::inList('field,list', $this->getParent()->getInlineFieldType($fieldCOnfiguration))) {
			return t3lib_utility_Dependency_Element::RESPONSE_Skip;
		}
	}

	public function createNewDependentElementParentReferenceCallback(array $callerArguments, array $targetArgument, t3lib_utility_Dependency_Element $caller, $eventName) {
		/** @var $reference t3lib_utility_Dependency_Reference */
		$reference = $callerArguments['reference'];

		$fieldCOnfiguration = t3lib_BEfunc::getTcaFieldConfiguration($reference->getElement()->getTable(), $reference->getField());

		if (!$fieldCOnfiguration || !t3lib_div::inList('field,list', $this->getParent()->getInlineFieldType($fieldCOnfiguration))) {
			return t3lib_utility_Dependency_Element::RESPONSE_Skip;
		}
	}

	/**
	 * @param t3lib_utility_Dependency_Element $caller
	 * @param  $callerArguments
	 * @param  $targetArgument
	 * @return void
	 */
	public function createNewDependentElementCallback(array $callerArguments, array $targetArgument, t3lib_utility_Dependency_Element $caller) {
		if ($caller->hasDataValue('liveId') === FALSE) {
			$liveId = t3lib_BEfunc::getLiveVersionIdOfRecord($caller->getTable(), $caller->getId());
			if (is_null($liveId) === FALSE) {
				$caller->setDataValue('liveId', $liveId);
			}
		}
	}

	/**
	 * Transforms dependent elements to use the liveId as array key.
	 *
	 * @param array $elements Depedent elements, each of type t3lib_utility_Dependency_Element
	 * @return array
	 */
	protected function transformDependentElementsToUseLiveId(array $elements) {
		$transformedElements = array();

		/** @var $element t3lib_utility_Dependency_Element */
		foreach ($elements as $element) {
			$elementName = t3lib_utility_Dependency_Element::getIdentifier(
				$element->getTable(), $element->getDataValue('liveId')
			);
			$transformedElements[$elementName] = $element;
		}

		return $transformedElements;
	}

	protected function constructScopes() {
		$this->scopes = array(
			self::SCOPE_WorkspacesSwap => array(
				self::KEY_ScopeErrorMessage => 'Record "%s" (%s:%s) cannot be swapped or published independently, because it is related to other new or modified records.',
				self::KEY_ScopeErrorCode => 1288283630,
				self::KEY_GetElementPropertiesCallback => 'getElementSwapPropertiesCallback',
				self::KEY_GetCommonPropertiesCallback => 'getCommonSwapPropertiesCallback',
			),
			self::SCOPE_WorkspacesSetStage => array(
				self::KEY_ScopeErrorMessage => 'Record "%s" (%s:%s) ...',
				self::KEY_ScopeErrorCode => 1289342524,
				self::KEY_GetElementPropertiesCallback => '',
				self::KEY_GetCommonPropertiesCallback => '',
			),
		);
	}

	/**
	 * @throws RuntimeException
	 * @param string $scope
	 * @param string $key
	 * @return string
	 */
	protected function getScopeData($scope, $key) {
		if (!isset($this->scopes[$scope])) {
			throw new RuntimeException('Scope "' . $scope . '" is not defined.', 1289342187);
		}

		return $this->scopes[$scope][$key];
	}
}
