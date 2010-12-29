<?php
/**
 * File contains class CCacheStorage
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Class CCacheStorage
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
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
	 * @param IStorageModel $model
	 * @return void
	 * @todo check result of insert
	 */
	public function insert(IStorageModel $model)
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
	 * @param IStorageModel $model
	 */
	public function update(IStorageModel $model)
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
		Yii::beginProfile(__METHOD__, 'ext.storage');

		$data = $this->getCache()->get($this->getKey($type, $primaryKey));

		Yii::endProfile(__METHOD__, 'ext.storage');
		
		return $data;
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
		Yii::beginProfile(__METHOD__, 'ext.storage');
		$list = array();
		
		$listKey = $this->getListKey($type, $primaryKeyList);
		$data = $this->getCache()->mget($listKey);
		
		foreach ($data as $item)
			if($item instanceof $type)
				$list[] = $item;

		Yii::endProfile(__METHOD__, 'ext.storage');
		
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
	 * @param IStorageModel $model
	 * @return string
	 */
	protected function getModelKey(IStorageModel $model)
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
	/**
	 * @param  $type
	 * @param array $primaryKeyList
	 * @return array
	 */
	protected function getListKey($type, array $primaryKeyList)
	{
		$list = array();
		foreach ($primaryKeyList as $primaryKey)
			$list[] = $this->getKey($type, $primaryKey);

		return $list;
	}
}
