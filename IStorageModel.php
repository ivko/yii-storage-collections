<?php
/**
 * File contains interface IStorageModel
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @link https://github.com/mitallast/yii-storage-collections
 * @copyright Alexey Korchevsky <mitallast@gmail.com> 2010-2011
 * @license https://github.com/mitallast/yii-storage-collections/blob/master/license
 */

/**
 * Interface IStorageModel
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @package ext.datamapper
 * @version 0.1
 * @since 0.1
 */
interface IStorageModel
{
	/**
	 * @return array attributes hash ( name => value )
	 * @abstract
	 */
	function getStorageAttributes();
	/**
	 * @param array $attributes attributes hash ( name => value )
	 * @abstract
	 */
	function setStorageAttributes(array $attributes);
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
