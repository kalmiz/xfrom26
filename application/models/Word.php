<?php

class Xfrom26_Model_Word extends Xfrom26_Model_Base
{
	protected $_word;
	protected $_profile;
	protected $_unit;
	protected $_pos;
	protected $_isDouble;
	protected $_length;
	protected $_definition;
	protected $_example;

	public function setWord($value) {
		$this->_word = $value;
		return $this;
	}

	public function getWord() {
		return $this->_word;
	}

	public function setProfile($value) {
		$this->_profile = $value;
		return $this;
	}

	public function getProfile() {
		return $this->_profile;
	}

	public function setUnit($value) {
		$this->_unit = (int)$value;
		return $this;
	}

	public function getUnit() {
		return (int)$this->_unit;
	}

	public function setPos($value) {
		$this->_pos = $value;
		return $this;
	}

	public function getPos() {
		return $this->_pos;
	}

	public function setIsDouble($value) {
		$this->_isDouble = (int)$value;
		return $this;
	}

	public function getIsDouble() {
		return (int)$this->_isDouble;
	}

	public function setLength($value) {
		$this->_length = (int)$value;
		return $this;
	}

	public function getLength() {
		return (int)$this->_length;
	}

	public function setExample($value) {
		$this->_example = $value;
		return $this;
	}

	public function getExample() {
		return $this->_example;
	}

	public function setDefinition($value) {
		$this->_definition = $value;
		return $this;
	}

	public function getDefinition() {
		return $this->_definition;
	}


}
