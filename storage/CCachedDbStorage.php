<?php
/**
 * File contains class CCachedDbStorage
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @link https://github.com/mitallast/yii-storage-collections
 * @copyright Alexey Korchevsky <mitallast@gmail.com> 2010-2011
 * @license https://github.com/mitallast/yii-storage-collections/blob/master/license
 */

/**
 * Class CCachedDbStorage. Storage optimized to work with database using cache component.
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @package ext.datamapper.storage
 * @version 0.1
 * @since 0.1
 */
class CCachedDbStorage extends CDbStorage
{
	/**
	 * @var CCacheStorage
	 */
	protected $_cache;
	/**
	 * Get cache component.
	 *
	 * @throws CStorageException if invalid cache setted
	 * @return CCacheStorage
	 */
	public function getCache()
	{
		if(!($this->_cache instanceof CCacheStorage))
		{
			if(is_string($this->_cache))
			{
				$this->_cache = Yii::app()->getComponent($this->_cache);
			}
			elseif(is_array($this->_cache))
			{
				$this->_cache = Yii::createComponent($this->_cache);
			}
			elseif(is_null($this->_cache))
			{
				$this->_cache = Yii::app()->getCache();
			}
			else
			{
				throw new CStorageException(
					Yii::t('ext.storage','Cache is not instance of CCacheStorage, component id or config array')
				);
			}

			if(!($this->_cache instanceof CCacheStorage))
			{
				throw new CStorageException(Yii::t('ext.storage','Cache is not instance of CCacheStorage'));
			}
		}
		return $this->_cache;
	}
	/**
	 * Set cache component.
	 * If it's set string, storage get cache component by id ( Yii::app()->getComponent($id) ).
	 * Else if it's set array, storage create cache component ( Yii::createComponent($config) ).
	 * Else if it's set null, storage get default app cache component ( Yii::app()->getCache() ).
	 *
	 * @param CCache|string|null $cache
	 * @return void
	 */
	public function setCache($cache)
	{
		$this->_cache = $cache;
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
		if( parent::insert($model) )
		{
			if($this->isModelHasExpressionValue($model))
			{
				$model = parent::findByPk(get_class($model), $model->getPrimaryKey());
			}
			$this->getCache()->update($model);
		}
	}
	/**
	 * Update model in storage
	 *
	 * @param IStorageModel $model
	 */
	public function update(IStorageModel $model)
	{
		if(parent::update($model))
		{
			if($this->isModelHasExpressionValue($model))
			{
				$model = parent::findByPk(get_class($model), $model->getPrimaryKey());
			}
			$this->getCache()->update($model);
		}
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
		parent::delete($type,$primaryKey);
		$this->getCache()->delete($type,$primaryKey);
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
		return $this->getCache()->exists($type, $primaryKey)
			or parent::exists($type, $primaryKey);
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
		$model = $this->getCache()->findByPk($type, $primaryKey);
		if(!($model))
		{
			$model = parent::findByPk($type, $primaryKey);
			if($model)
				$this->getCache()->update($model);
		}

		return $model;
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
		$pkHash = array();
		foreach($primaryKeyList as $key) $pkHash[$key] = $key;

		$models = $this->getCache()->findAllByPk($type, $primaryKeyList);
		$modelsHash = array();
		foreach($models as $model)
		{
			unset($pkHash[$model->getPrimaryKey()]);
			$modelsHash[$model->getPrimaryKey()] = $model;
		}

		if(count($modelsHash) < count($primaryKeyList))
		{
			$models = parent::findAllByPk($type, $pkHash);
			foreach($models as $model)
			{
				$modelsHash[$model->getPrimaryKey()] = $model;
				$this->getCache()->update($model);
			}
		}

		$list = array();
		foreach($primaryKeyList as $key)
		{
			if(isset($modelsHash[$key]))
				$list[] = $modelsHash[$key];
		}

		return $list;
	}
	/**
	 * @param IStorageModel $model
	 * @return bool
	 */
	protected function isModelHasExpressionValue(IStorageModel $model)
	{
		$data = $model->getStorageAttributes();

		$hasExpression = false;
		foreach($data as $value)
		{
			if(is_object($value))
			{
				$hasExpression = true;
				break;
			}
		}
		return $hasExpression;
	}
}