<?php
class t3lib_utility_Dependency_Element {
	const REFERENCES_ChildOf = 'childOf';
	const REFERENCES_ParentOf = 'parentOf';
	const EVENT_Construct = 't3lib_utility_Dependency_Element::construct';
	const EVENT_CreateChildReference = 't3lib_utility_Dependency_Element::createChildReference';
	const EVENT_CreateParentReference = 't3lib_utility_Dependency_Element::createParentReference';
	const RESPONSE_Skip = 't3lib_utility_Dependency_Element->skip';

	protected $table;
	protected $id;
	protected $data;

	/**
	 * @var t3lib_utility_Dependency
	 */
	protected $dependency;

	protected $children;
	protected $parents;

	protected $traversingParents = FALSE;
	protected $outerMostParent;
	protected $nestedChildren;

	public function __construct($table, $id, array $data = array(), t3lib_utility_Dependency $dependency) {
		$this->table = $table;
		$this->id = intval($id);
		$this->data = $data;
		$this->dependency = $dependency;

		$this->dependency->executeEventCallback(self::EVENT_Construct, $this);
	}

	public function getTable() {
		return $this->table;
	}

	public function getId() {
		return $this->id;
	}

	public function getData() {
		return $this->data;
	}

	public function getDataValue($key) {
		$result = NULL;

		if ($this->hasDataValue($key)) {
			$result = $this->data[$key];
		}

		return $result;
	}

	public function setDataValue($key, $value) {
		$this->data[$key] = $value;
	}

	public function hasDataValue($key) {
		return (isset($this->data[$key]));
	}

	public function __toString() {
		return self::getIdentifier($this->table, $this->id);
	}

	/**
	 * @return t3lib_utility_Dependency
	 */
	public function getDependency() {
		return $this->dependency;
	}

	public function getChildren() {
		if (!isset($this->children)) {
			$this->children = array();
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				'sys_refindex',
				'tablename=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->table, 'sys_refindex') .
					' AND recuid=' . $this->id
			);
			if (is_array($rows)) {
				foreach ($rows as $row) {
					$reference = $this->getFactory()->getReferencedElement(
						$row['ref_table'], $row['ref_uid'], $row['field'], array(), $this->getDependency()
					);
					$callbackResponse = $this->dependency->executeEventCallback(
						self::EVENT_CreateChildReference,
						$this, array('reference' => $reference)
					);
					if ($callbackResponse !== self::RESPONSE_Skip) {
						$this->children[] = $reference;
					}
				}
			}
		}
		return $this->children;
	}

	public function getParents() {
		if (!isset($this->parents)) {
			$this->parents = array();
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				'sys_refindex',
				'ref_table=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->table, 'sys_refindex') .
					' AND deleted=0 AND ref_uid=' . $this->id
			);
			if (is_array($rows)) {
				foreach ($rows as $row) {
					$reference = $this->getFactory()->getReferencedElement(
						$row['tablename'], $row['recuid'], $row['field'], array(), $this->getDependency()
					);
					$callbackResponse = $this->dependency->executeEventCallback(
						self::EVENT_CreateParentReference,
						$this, array('reference' => $reference)
					);
					if ($callbackResponse !== self::RESPONSE_Skip) {
						$this->parents[] = $reference;
					}
				}
			}
		}
		return $this->parents;
	}

	/**
	 * @return boolean
	 */
	public function hasReferences() {
		return (count($this->getChildren()) > 0 && count($this->getParents()) > 0);
	}

	/**
	 * @return t3lib_utility_Dependency_Element
	 */
	public function getOuterMostParent() {
		if (!isset($this->outerMostParent)) {
			$parents = $this->getParents();
			if (count($parents) === 0) {
				$this->outerMostParent = $this;
			} else {
				$this->outerMostParent = FALSE;
				/** @var $parent t3lib_utility_Dependency_Reference */
				foreach ($parents as $parent) {
					$outerMostParent = $parent->getElement()->getOuterMostParent();
					if ($outerMostParent instanceof t3lib_utility_Dependency_Element) {
						$this->outerMostParent = $outerMostParent;
						break;
					} elseif ($outerMostParent === FALSE) {
						break;
					}
				}
			}
		}

		return $this->outerMostParent;
	}

	public function getNestedChildren() {
		if (!isset($this->nestedChildren)) {
			$this->nestedChildren = array();
			$children = $this->getChildren();
			/** @var $child t3lib_utility_Dependency_Reference */
			foreach ($children as $child) {
				$this->nestedChildren = array_merge(
					$this->nestedChildren,
					array($child->getElement()->__toString() => $child->getElement()),
					$child->getElement()->getNestedChildren()
				);
			}
		}

		return $this->nestedChildren;
	}

	/**
	 * @return t3lib_utility_Dependency_Factory
	 */
	protected function getFactory() {
		return t3lib_div::makeInstance('t3lib_utility_Dependency_Factory');
	}

	public static function getIdentifier($table, $id) {
		return $table . ':' . $id;
	}
}