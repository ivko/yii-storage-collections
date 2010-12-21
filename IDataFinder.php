<?php

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
