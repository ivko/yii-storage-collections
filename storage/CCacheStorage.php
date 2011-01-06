<?php
/**
 * File contains class CCacheStorage
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @link https://github.com/mitallast/yii-storage-collections
 * @copyright Alexey Korchevsky <mitallast@gmail.com> 2010-2011
 * @license https://github.com/mitallast/yii-storage-collections/blob/master/license
 */

/**
 * Class CCacheStorage
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @package ext.datamapper.storage
 * @version 0.1
 * @since 0.1
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
		$result = $this->getCache()->set($key,$model);

		return $result;

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
		$list = array();

		$listKey = $this->getListKey($type, $primaryKeyList);
		$data = $this->getCache()->mget($listKey);

		foreach ($data as $item)
			if($item instanceof $type)
				$list[] = $item;

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
