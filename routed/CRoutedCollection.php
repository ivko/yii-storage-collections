<?php
/**
 * File contains class CShardCollection
 * 
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CRoutedException.php";

/**
 * Collection implements routed storage list.
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @abstract
 */
abstract class CRoutedCollection extends CBaseStorageCollection implements IDataStorage
{
	/**
	 * Delete model from storage
	 *
	 * @param string $type
	 * @param mixed $primaryKey
	 * @return int
	 */
	public function delete($type, $primaryKey)
	{
		$id = $this->getShardIdForDelete($type, $primaryKey);
		$this->itemAt((int)$id)->delete($type, $primaryKey);
	}
	/**
	 * Check exists model in storage by pk
	 *
	 * @param  $type
	 * @param  $primaryKey
	 * @return bool
	 */
	public function exists($type, $primaryKey)
	{
		$id = $this->getShardIdForSelect($type, $primaryKey);
		$this->itemAt((int)$id)->exists($type, $primaryKey);
	}
	/**
	 * Get models by primary key list
	 *
	 * @param  $type
	 * @param array $primaryKeyList
	 * @return IModelStorage[]
	 */
	public function findAllByPk($type, array $primaryKeyList)
	{
		$list = array();

		$hashList = array();
		foreach($primaryKeyList as $key)
		{
			$id = $this->getShardIdForSelect($type, $key);
			if(!isset($hashList[$id]))
				$hashList[$id] = array($key);
			else
				$hashList[$id][] = $key;
		}

		foreach($hashList as $id => $listKey)
		{
			$data = $this->itemAt($id)->findAllByPk($type, $listKey);
			$list = array_merge($list, $data);
		}

		return $list;
	}
	/**
	 * Get model by primary key
	 *
	 * @param  $type
	 * @param  $primaryKey
	 * @return void
	 */
	public function findByPk($type, $primaryKey)
	{
		return $this
			->getShardIdForSelect($type, $primaryKey)
			->findByPk($type, $primaryKey);
	}
	/**
	 * Save model in storage
	 *
	 * @param IStorageModel $model
	 * @return void
	 */
	public function insert(IStorageModel $model)
	{
		return $this
			->getShardIdForCreate(get_class($model))
			->insert($model);
	}
	/**
	 * Update model in storage
	 *
	 * @param IStorageModel $model
	 */
	public function update(IStorageModel $model)
	{
		return $this
			->getShardForUpdate(get_class($model), $model->getPrimaryKey())
			->update($model);
	}
	/**
	 * Get storage component for create new model. 
	 * As example, if application using sharding, rule can be $id = ( microtime(1) * 1000000) % $this->count.
	 * 
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardIdForCreate($type);
	/**
	 * Get storage component for update model.
	 * As example, rule (int)$primaryKey % $this->count().
	 *
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardForUpdate($type, $primaryKey);
	/**
	 * Get storage component for delete model by primary key.
	 * As default, you can use logic of getShardForUpdate.
	 * 
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardIdForDelete($type, $primaryKey);
	/**
	 * Get storage component for select model by primary key. 
	 * As default, you can use logic of getShardForUpdate.
	 *
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardIdForSelect($type, $primaryKey);
}
