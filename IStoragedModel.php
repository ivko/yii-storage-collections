<?php

interface IStoragedModel
{
	/**
	 * @return array attributes hash ( name => value )
	 * @abstract
	 */
	function getStoragedAttributes();
	/**
	 * @param array $attrs  attributes hash ( name => value )
	 * @abstract
	 */
	function setStoragedAttributes(array $attrs);
	/**
	 * @return mixed
	 * @abstract
	 */
	function getPrimaryKey();
	/**
	 * @param mixed $key
	 * @abstract
	 */
	function setPrimaryKey($key);
}
