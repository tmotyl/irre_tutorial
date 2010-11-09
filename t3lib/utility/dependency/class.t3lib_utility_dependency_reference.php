<?php
class t3lib_utility_Dependency_Reference {
	/**
	 * @var t3lib_utility_Dependency_Element
	 */
	protected $element;
	protected $field;

	public function __construct(t3lib_utility_Dependency_Element $element, $field) {
		$this->element = $element;
		$this->field = $field;
	}

	public function getElement() {
		return $this->element;
	}

	public function getField() {
		return $this->field;
	}

	public function __toString() {
		return $this->element . '.' . $this->field;
	}
}