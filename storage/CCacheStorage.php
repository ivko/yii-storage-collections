<?php
/**
 * Created by JetBrains PhpStorm.
 * User: php2
 * Date: 14.12.10
 * Time: 11:12
 * To change this template use File | Settings | File Templates.
 */
 
class CCacheStorage implements IDataStorage
{
	/**
	 * @var CCache
	 */
	private $_cache;

	/**
	 * Save model in storage
	 *
	 * @param IStoragedModel $model
	 * @return void
	 * @todo check result of insert
	 */
	public function insert(IStoragedModel $model)
	{
		if(is_null($model->getPrimaryKey()))
		{
			$model->setPrimaryKey(microtime(1));
		}

		$key = $this->getModelKey($model);
		return $this->getCache()->set($key,$model);
	}
	/**
	 * Update model in storage
	 *
	 * @param IStoragedModel $model
	 */
	public function update(IStoragedModel $model)
	{
		if(is_null($model->getPrimaryKey()))
			throw new CStorageException(Yii::t("db.storage","Primary key is not setted"));

		$this->getCache()->set(
			$this->getModelKey($model),
			$model
		);
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
		$this->getCache()->delete(
			$this->getKey($type, $primaryKey)
		);
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
		$key = $this->getKey($type, $primaryKey);
		return $this->getCache()->get($key) !== false;
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
		return $this->getCache()->get($this->getKey($type, $primaryKey));
	}
	/**
	 * Get models by primary key list
	 *
	 * @param  $type
	 * @param array $primaryKeyList
	 * @return void
	 */
	public function findAllByPk($type, array $primaryKeyList)
	{
		$list = new CDataCollection;
		foreach($primaryKeyList as $key)
		{
			$model = $this->findByPk($type, $key);
			if($model)
				$list->add($model);
		}
		return $list;
	}
	/**
	 * @return CCache
	 */
	public function getCache()
	{
		if(is_null($this->_cache))
			$this->setCache("cache");

		return $this->_cache;
	}
	/**
	 * @param string|CCache $cache cache component instance or id in Yii::app()
	 * @throw CStorageException if component not found or not instance of CCache
	 */
	public function setCache($cache)
	{
		if(!is_object($cache))
		{
			$cache = (string)$cache;
			$cache = Yii::app()->getComponent($cache);
		}

		if($cache instanceof CCache)
			$this->_cache = $cache;
		else
			throw new CStorageException(Yii::t("db.storage", "Cache component is not instance of CCache"));
	}
	/**
	 * @param IStoragedModel $model
	 * @return string
	 */
	protected function getModelKey(IStoragedModel $model)
	{
		return $this->getKey(
			get_class($model),
			$model->getPrimaryKey()
		);
	}
	/**
	 * @param string $type
	 * @param string $primaryKey
	 * @return string
	 */
	protected function getKey($type, $primaryKey)
	{
		return md5(join(":",array(
			$type,
			$primaryKey,
		)));
	}
}
