<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 09.11.10
 * Time: 15:01
 * To change this template use File | Settings | File Templates.
 */
 
class t3lib_utility_Dependency_Callback {
	protected $object;
	protected $method;
	protected $targetArguments;

	public function __construct($object, $method, array $targetArguments = array()) {
		$this->object = $object;
		$this->method = $method;
		$this->targetArguments = $targetArguments;
		$this->targetArguments['target'] = $object;
	}

	public function execute(array $callerArguments = array(), $caller, $eventName) {
		return call_user_func_array(
			array($this->object, $this->method),
			array($callerArguments, $this->targetArguments, $caller, $eventName)
		);
	}
}
