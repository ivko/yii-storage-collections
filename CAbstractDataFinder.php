<?php
/**
 * File contains class CAbstractDataFinder
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Class ESphinxFinderCriteria implements base for creating data mapper with custom find logic.
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @abstract
 * @throws CStorageException
 */
abstract class CAbstractDataFinder extends CComponent implements IDataFinder
{
	private $_storage;
	/**
	 * Get setted storage
	 *
	 * @return IDataStorage $collection - can be simple storage and storage collection
	 * @see setStorage
	 * @see findModels
	 */
	public function getStorage()
	{
		return $this->_storage;
	}

	/**
	 * Set storage. It's used when need return models list.
	 *
	 * @param IDataStorage|string $collection - can be simple storage, storage collection, or id of yii app component
	 * @throws CStorageException
	 * @see getStorage
	 * @see findModels
	 */
	public function setStorage($storage)
	{
		if(is_string($storage))
		{
			$storage = Yii::app()->getComponent($storage);
		}

		if($storage instanceof IDataStorage)
			$this->_storage = $storage;
		else
			throw new CStorageException(Yii::t("db.finder", "\$storage is not instance of IDataStorage"));
	}
}
