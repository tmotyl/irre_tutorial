<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 09.11.10
 * Time: 14:58
 * To change this template use File | Settings | File Templates.
 */
 
class t3lib_utility_Dependency_Factory implements t3lib_Singleton {
	protected $elements = array();
	protected $references = array();

	/**
	 * @param  $table
	 * @param  $id
	 * @param null|t3lib_utility_Dependency_Callback $callback
	 * @return t3lib_utility_Dependency_Element
	 */
	public function getElement($table, $id, array $data = array(), t3lib_utility_Dependency $dependency) {
		$elementName = $table . ':' . $id;
		if (!isset($this->elements[$elementName])) {
			$this->elements[$elementName] = t3lib_div::makeInstance(
				't3lib_utility_Dependency_Element',
				$table, $id, $data, $dependency
			);
		}
		return $this->elements[$elementName];
	}

	/**
	 * @param t3lib_utility_Dependency_Element $element
	 * @param  $field
	 * @return t3lib_utility_Dependency_Reference
	 */
	public function getReference(t3lib_utility_Dependency_Element $element, $field) {
		$referenceName = $element->__toString() . '.' . $field;
		if (!isset($this->references[$referenceName][$field])) {
			$this->references[$referenceName][$field] = t3lib_div::makeInstance(
				't3lib_utility_Dependency_Reference',
				$element, $field
			);
		}
		return $this->references[$referenceName][$field];
	}

	/**
	 * @param  $table
	 * @param  $id
	 * @param  $field
	 * @param null|t3lib_utility_Dependency_Callback $callback
	 * @return t3lib_utility_Dependency_Reference
	 */
	public function getReferencedElement($table, $id, $field, array $data = array(), t3lib_utility_Dependency $dependency) {
		return $this->getReference(
			$this->getElement($table, $id, $data, $dependency),
			$field
		);
	}
}
