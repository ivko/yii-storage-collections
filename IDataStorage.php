<?php


interface IDataStorage
{
	/**
	 * Save model in storage
	 *
	 * @param IStoragedModel $model
	 * @return void
	 * @todo check result of insert
	 */
	function insert(IStoragedModel $model);
	/**
	 * Update model in storage
	 *
	 * @param IStoragedModel $model
	 */
	function update(IStoragedModel $model);
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
	 * @return void
	 */
	function findAllByPk($type, array $primaryKeyList);
}
