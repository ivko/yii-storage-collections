<?php

class CDbStorage implements IDataStorage
{
	/**
	 * @var CDbConnection $_db;
	 * @see CDbConnection
	 * @see getDbConnection
	 */
	private $_db;
	/**
	 * @var CMap $_model_table
	 */
	private $_model_table;
	/**
	 * @var CMap $_model_table
	 */
	private $_model_table_pk;
	/**
	 * Inicialise storage
	 */
	public function __construct()
	{
		$this->_model_table = new CMap;
	}
	/**
	 * Save model in storage
	 *
	 * @param IStoragedModel $model
	 * @return void
	 * @todo check result of insert
	 */
	public function insert(IStoragedModel $model)
	{
		$cb = $this->getDbConnection()->getCommandBuilder();
		$table = $this->getModelTable(get_class($model));
		$data = $model->getAttributes();

		$insert = $cb->createInsertCommand($table, $data);
		$result = $insert->execute();

		$model->setPrimaryKey( $this->getDbConnection()->getLastInsertID() );
		return $result;
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

		$cb = $this->getDbConnection()->getCommandBuilder();
		$table = $this->getModelTable(get_class($model));
		$data = $model->getAttributes();

		$primaryKey = $model->getPrimaryKey();
		$update = $cb->createUpdateCommand($table, $data, $this->getPkCriteria($table, $primaryKey));
		return $update->execute();
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
		$cb = $this->getDbConnection()->getCommandBuilder();
		$table = $this->getModelTable((string)$type);

		return $cb
			->createDeleteCommand($table, $this->getPkCriteria($table, $primaryKey))
			->execute();
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
		$cb = $this->getDbConnection()->getCommandBuilder();
		$table = $this->getModelTable((string)$type);

		$count = $cb
			->createCountCommand($table, $this->getPkCriteria($table, $primaryKey))
			->queryScalar();

		return (int)$count > 0;
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
		$cb = $this->getDbConnection()->getCommandBuilder();
		$table = $this->getModelTable((string)$type);

		$find = $cb->createFindCommand($table,$this->getPkCriteria($table, $primaryKey));
		$row =	$find->queryRow();

		if(!$row)
			return null;
		
		$model = new $type;
		$model->setStoragedAttributes($row);
		return $model;
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
		$cb = $this->getDbConnection()->getCommandBuilder();
		$table = $this->getModelTable((string)$type);

		$rows = $cb
			->createFindCommand($table,$this->getPkCriteria($table, $primaryKeyList))
			->queryAll();

		$list = new CDataCollection;
		
		foreach($rows as $row)
		{
			$model = new $type;
			$model->setStoragedAttributes($row);
			$list->add($model);
		}

		return $list;
	}
	/**
	 * @throws CStorageException if component not found of is not instance of CDbConnection
	 * @param string|CDbConnection $component id of Yii::app() component, or object of CDbConnection
	 * @see getDbConnection
	 */
	public function setDbConnection($component)
	{
		if(!is_object($component))
		{
			$component = (string)$component;
			$component = Yii::app()->getComponent((string)$component);
		}

		if($component instanceof CDbConnection)
		{
			$this->_db = $component;
			$this->_model_table_pk = new CMap;
		}
		else
			throw new CStorageException(
				Yii::t("db.storage", "\$connection is not instance of CDbConnection")
			);
	}
	/**
	 * @return CDbConnection
	 * @see CDbConnection
	 */
	public function getDbConnection()
	{
		if(is_null($this->_db))
			$this->setDbConnection("db");

		return $this->_db;
	}
	/**
	 * Set hash model name to table alias
	 * @example  array( "Model" => "{{model}}", "Form" => "base_form")
	 * @param array $list
	 */
	public function setModelTables(array $list)
	{
		foreach($list as $model => $table)
			$this->setModelTable($model, $table);
	}
	/**
	 * Set association model class to database table
	 * @param  $modelName
	 * @param  $tableName
	 */
	public function setModelTable($modelName, $tableName)
	{
		$this
			->_model_table
			->add((string)$modelName, (string)$tableName);
	}
	/**
	 * Get associatived with model class table name
	 * @throws CStorageException if model not found
	 * @param  $modelName
	 * @return string
	 */
	public function getModelTable($modelName)
	{
		if($this->_model_table->offsetExists($modelName))
			return $this
				->_model_table
				->itemAt($modelName);
		else
			throw new CStorageException(
				Yii::t("db.storage","Model table not found")
			);
	}
	/**
	 * Get primary key name by table name
	 *
	 * @param  $table
	 * @return mixed
	 */
	private function getPrimaryKey($table)
	{
		if(!$this->_model_table_pk->offsetExists($table))
		{
			$primaryKey = $this->getDbConnection()
				->getSchema()
				->getTable($table)
				->primaryKey;

			$this->_model_table_pk->add($table, $primaryKey);
		}
		
		return $this->_model_table_pk->itemAt($table);
	}
	/**
	 * Create criteria object by table name and primary key
	 *
	 * @param  $table
	 * @param  $primaryKey
	 * @return CDbCriteria
	 */
	private function getPkCriteria($table, $primaryKey)
	{
		$criteria = new CDbCriteria;
		$criteria->compare(
			$this->getPrimaryKey($table),
			$primaryKey
		);
		return $criteria;
	}
}