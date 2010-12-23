<?php
/**
 * File contains interface IDataFinder
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Interface IDataFinder
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
 */
interface IDataFinder
{
	/**
	 * Get setted storage
	 *
	 * @return IDataStorage $collection - can be simple storage and storage collection
	 * @see setStorage
	 * @see findModels
	 */
	function getStorage();

	/**
	 * Set storage. It's used when need return models list.
	 *
	 * @param IDataStorage|string $collection - can be simple storage, storage collection, or id of yii app component
	 * @see getStorage
	 * @see findModels
	 */
	function setStorage($storage);
}
