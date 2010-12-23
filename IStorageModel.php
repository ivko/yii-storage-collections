<?php
/**
 * File contains interface IStorageModel
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Interface IStorageModel
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
 */
interface IStorageModel
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
