<?php
/**
 * File contains class CStorageCollection
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CBaseStorageCollection.php";

/**
 * Class CStorageCollection
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
 */
class CStorageCollection extends CBaseStorageCollection implements IDataStorage
{

	/**
	 * Insert new model and set it primary key
	 *
	 * @param IStorageModel $model
	 * @return void
	 */
	public function insert(IStorageModel $model)
	{
		/** @var $storage CDbStorage */
		foreach($this as $storage)
			$storage->insert($model);
	}
	/**
	 * Update model by setted primary key
	 *
	 * @param IStorageModel $model
	 * @return void
	 */
	public function update(IStorageModel $model)
	{
		/** @var $storage CDbStorage */
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
		/** @var $storage CDbStorage */
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
		/** @var $storage CDbStorage */
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
	 * @return IStorageModel
	 */
	public function findByPk($type, $primaryKey)
	{
		$model = null;
		
		/** @var $storage CDbStorage */
		foreach($this as $storage)
		{
			if(($item = $storage->findByPk($type, $primaryKey)) instanceof $type)
			{
				$model = $item;
				break;
			}
		}

		return $model;
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
		$findList = array();
		foreach ($primaryKeyList as $key)
			$findList[$key] = $key;
		
		$list = array();
		foreach ($this as $storage)
		{
			$data = $storage->findAllByPk($type, $findList);
			if(is_array($data))
			{
				foreach($data as $model)
					unset($findList[$model->getPrimaryKey()]);
				$list = array_merge($list, $data);
			}

			if(count($data) == count($primaryKeyList))
				break;
		}

		return $list;
	}
}
