<?php
/**
 * File contains class CShardCollection
 * 
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Collection implements routed storage list.
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @abstract
 */
abstract class CRoutedCollection extends CTypedList implements IDataStorage
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct("IDataStorage");
	}
	
	/**
	 * Delete model from storage
	 *
	 * @param string $type
	 * @param mixed $primaryKey
	 * @return int
	 */
	public function delete($type, $primaryKey)
	{
		$this->getShardForDelete($type, $primaryKey)->delete($type, $primaryKey);
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
		$this->getShardForSelect($type, $primaryKey)->exists($type, $primaryKey);
	}

	/**
	 * Get models by primary key list
	 *
	 * @param  $type
	 * @param array $primaryKeyList
	 * @return void
	 * @todo add implementation
	 */
	public function findAllByPk($type, array $primaryKeyList)
	{
		$list = new CDataCollection;
		foreach($primaryKeyList as $key)
			if($model = $this->findByPk($type, $key))
				$list->add($model);
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
			->getShardForSelect($type, $primaryKey)
			->findByPk($type, $primaryKey);
	}

	/**
	 * Save model in storage
	 *
	 * @param IStorageModel $model
	 * @return void
	 * @todo check result of insert
	 */
	public function insert(IStorageModel $model)
	{
		if(is_null($model->getPrimaryKey()))
		{
			$key = $this->generatePrimaryKey();
			if(is_null($key))
			{
				throw new CRoutedException(Yii::t('mapper.routed', 'Primary key is null'));
			}
			$model->setPrimaryKey($key);
		}

		return $this
			->getShardForCreate(get_class($model))
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
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardForCreate($type);
	/**
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardForUpdate($type, $primaryKey);
	/**
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardForDelete($type, $primaryKey);
	/**
	 * @abstract
	 * @return IDataStorage
	 */
	abstract protected function getShardForSelect($type, $primaryKey);

	abstract protected function generatePrimaryKey();

}