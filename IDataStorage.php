<?php
/**
 * File contains interface IDataStorage
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Interface IDataStorage
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
 */
interface IDataStorage
{
	/**
	 * Save model in storage
	 *
	 * @param IStorageModel $model
	 * @return void
	 * @todo check result of insert
	 */
	function insert(IStorageModel $model);
	/**
	 * Update model in storage
	 *
	 * @param IStorageModel $model
	 */
	function update(IStorageModel $model);
	/**
	 * Delete model from storage
	 *
	 * @param string $type
	 * @param mixed $primaryKey
	 * @return int
	 */
	function delete($type, $primaryKey);
	/**
	 * Check exists model in storage by pk
	 *
	 * @param  $type
	 * @param  $primaryKey
	 * @return bool
	 */
	function exists($type, $primaryKey);
	/**
	 * Get model by primary key
	 *
	 * @param  $type
	 * @param  $primaryKey
	 * @return void
	 */
	function findByPk($type, $primaryKey);
	/**
	 * Get models by primary key list
	 *
	 * @param  $type
	 * @param array $primaryKeyList
	 * @return IModelStorage[]
	 */
	function findAllByPk($type, array $primaryKeyList);
}
