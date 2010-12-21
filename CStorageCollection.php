<?php

class CStorageCollection extends CTypedList implements IDataStorage
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct("IDataStorage");
	}
	/**
	 * Insert new model and set it primary key
	 *
	 * @param IStoragedModel $model
	 * @return void
	 */
	public function insert(IStoragedModel $model)
	{
		foreach($this as $storage)
			$storage->insert($model);
	}
	/**
	 * Update model by setted primary key
	 *
	 * @param IStoragedModel $model
	 * @return void
	 */
	public function update(IStoragedModel $model)
	{
		foreach($this as $storage)
			$storage->update($model);
	}
	/**
	 * Delete model by primary key
	 *
	 * @param string $type
	 * @param int $primaryKey
	 * @return void
	 */
	public function delete($type, $primaryKey)
	{
		foreach($this as $storage)
			$storage->delete($type, $primaryKey);
	}
	/**
	 * Check exists model by primary key
	 *
	 * @param string $type
	 * @param int $primaryKey
	 * @return bool
	 */
	public function exists($type, $primaryKey)
	{
		foreach($this as $storage)
			if($storage->exists($type, $primaryKey))
				return true;

		return false;
	}
	/**
	 * Get model by primary key
	 *
	 * @param string $type
	 * @param int $primaryKey
	 * @return IStoragedModel
	 */
	public function findByPk($type, $primaryKey)
	{
		foreach($this as $storage)
		{
			if(($model = $storage->findByPk($type, $primaryKey)) instanceof $type)
				return $model;
		}
		return null;
	}
	/**
	 * Get model collection by primary key
	 *
	 * @param string $type
	 * @param int[] $primaryKeyList
	 * @return void
	 */
	public function findAllByPk($type, array $primaryKeyList)
	{
		$list = new CDataCollection;
		foreach($primaryKeyList as $key)
			if($model = $this->findByPk($type, $key))
				$list->add($model);
		return $list;
	}
}
