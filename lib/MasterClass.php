<?php

/**
 * A default class with some useful methods to 
 *  - automagically call setter-
 *  - and getter functions, and
 *  - set options on initialization
 */
class MasterClass {
	/**
	 * instantiates a class and optionally sets a bunch of options
	 */
	public function __construct(array $options=null) {
		$this->SetOptions($options);
		if (get_parent_class(__CLASS__))
			return parent::__construct($options);
	}
	
	
	/**
	 * Overload default magic setter method to check for existing setter-
	 * functions first (e.g. for the key "fooBar"):
	 *  - public    function setFooBar($value);
	 *  - protected function _setFooBar($value);
	 *  - private   function setFooBar($value);
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return bool  $success
	 */
	public function __set($key, $value) {
		if (!property_exists($this, $key)) {
			trigger_error('Key not found: "'.$key.'"', E_USER_WARNING);
			return false;
		}
		
		if (method_exists($this, 'Set'.ucfirst($key))) {
			return $this->{'Set'.ucfirst($key)}($value);
		}
		
		if (method_exists($this, '_set'.ucfirst($key))) {
			return $this->{'_set'.ucfirst($key)}($value);
		}
		
		if (method_exists($this, 'set'.ucfirst($key))) {
			return $this->{'set'.ucfirst($key)}($value);
		}
		
		$this->$key = $value;
		return true;
	}
	
	
	/**
	 * Overload default magic getter method to check for existing getter-
	 * methods first (GetFooBar() for the key "fooBar").
	 *
	 * @param  string $key
	 * @return mixed $value
	 */
	public function __get($key) {
		if (!property_exists($this, $key)) {
			trigger_error('Key not found: "'.$key.'"', E_USER_WARNING);
			return false;
		}
		
		if (method_exists($this, 'Get'.ucfirst($key))) {
			return $this->{'Get'.ucfirst($key)}($value);
		}
		
		return $this->$key;
	}
	
	
	
	/**
	 * Iterates over an array and tries to set the keys.
	 * @example
	 *   $class->SetOptions(
	 *       array (
	 *           'someKey' => 'someValue',
	 *           'fooKey'  => 'fooValue'));
	 * @param array [$options]
	 */
	public function SetOptions(array $options=null) {
		if ($options == null)
			return;
		
		foreach ($options as $key => $value) {
			$this->$key = $value;
		}
	}
}
