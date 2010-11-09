<?php
class t3lib_utility_Dependency {
	protected $elements = array();
	protected $eventCallbacks = array();

	protected $outerMostParents;

	public function setEventCallback($eventName, t3lib_utility_Dependency_Callback $callback) {
		$this->eventCallbacks[$eventName] = $callback;
	}

	public function executeEventCallback($eventName, $caller) {
		if (isset($this->eventCallbacks[$eventName])) {
			/** @var $callback t3lib_utility_Dependency_Callback */
			$callback = $this->eventCallbacks[$eventName];
			$callback->execute($caller);
		}
	}

	public function addElement($table, $id, array $data = array()) {
		$element = $this->getFactory()->getElement($table, $id, $data, $this);
		$elementName = $element->__toString();
		$this->elements[$elementName] = $element;
	}

	/**
	 * @return array
	 */
	public function getOuterMostParents() {
		if (!isset($this->outerMostParents)) {
			$this->outerMostParents = array();

			/** @var $element t3lib_utility_Dependency_Element */
			foreach ($this->elements as $element) {
				$this->processOuterMostParent($element);
			}
		}

		return $this->outerMostParents;
	}

	protected function processOuterMostParent(t3lib_utility_Dependency_Element $element) {
		$outerMostParent = $element->getOuterMostParent();

		if ($outerMostParent !== FALSE) {
			$outerMostParentName = $outerMostParent->__toString();
			if (!isset($this->outerMostParents[$outerMostParentName])) {
				$this->outerMostParents[$outerMostParentName] = $outerMostParent;
			}
		}
	}

	/**
	 * @throws RuntimeException
	 * @param t3lib_utility_Dependency_Element $outerMostParent
	 * @return array
	 */
	public function getNestedElements(t3lib_utility_Dependency_Element $outerMostParent) {
		$outerMostParentName = $outerMostParent->__toString();

		if (!isset($this->outerMostParents[$outerMostParentName])) {
			throw new RuntimeException(
				'Element "' . $outerMostParentName . '" was detected as outermost parent.',
				1289318609
			);
		}

		$nestedStructure = array_merge(
			array($outerMostParentName => $outerMostParent),
			$outerMostParent->getNestedChildren()
		);

		return $nestedStructure;
	}

	public function getElements() {
		return $this->elements;
	}

	/**
	 * @return t3lib_utility_Dependency_Factory
	 */
	protected function getFactory() {
		return t3lib_div::makeInstance('t3lib_utility_Dependency_Factory');
	}
}
