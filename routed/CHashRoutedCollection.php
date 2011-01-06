<?php
/**
 * File contains class CHashRoutedCollection
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @link https://github.com/mitallast/yii-storage-collections
 * @copyright Alexey Korchevsky <mitallast@gmail.com> 2010-2011
 * @license https://github.com/mitallast/yii-storage-collections/blob/master/license
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CRoutedCollection.php";

/**
 * Class CHashRoutedCollection
 *
 * @author Alexey Korchevsky <mitallast@gmail.com>
 * @package ext.datamapper.routed
 * @version 0.1
 * @since 0.1
 */
class CHashRoutedCollection extends CRoutedCollection
{
	private $_idStep = 1;
	/**
	 * @return int
	 */
	public function getIdStep()
	{
		return (int)$this->_idStep;
	}
	/**
	 * Set count id step. Defaults to zero.
	 * If it setted > 1, list of models with some range id will be at one storage.
	 *
	 * Example:
	 * <code>
	 * $m = new CHashRoutedCollection;
	 * $m->setItems(array('shard1','shard2'));
	 * $m->setIdStep(10);
	 * $m->init();
	 *
	 * $m->findAll('model', array(1,2,3,4,5,6,7)); // use ((1..7)/10)%2 = 0 (shard1)
	 * $m->findAll('model', array(1,2,11,12)); // use ((1..2)/10)%2 = 0 (shard1) and ((11..12)/10)%2 = 1 (shard2)
	 * </code>
	 *
	 * @param int $step
	 */
	public function setIdStep($step)
	{
		$this->_idStep = (int)$step;
	}

	/**
	 * Get storage component for create new model.
	 * As example, if application using sharding, rule can be $id = ( time(1) * 1000000) % $this->count.
	 *
	 * @return IDataStorage
	 */
	protected function getShardIdForCreate($type)
	{
		return (int)time(1)%$this->count();
	}
	/**
	 * Get storage component for delete model by primary key.
	 * As default, you can use logic of getShardForUpdate.
	 *
	 * @return IDataStorage
	 */
	protected function getShardIdForDelete($type, $primaryKey)
	{
		return $this->getShardIdForSelect($type, $primaryKey);
	}

	/**
	 * Get storage component for select model by primary key.
	 * As default, you can use logic of getShardForUpdate.
	 *
	 * @return IDataStorage
	 */
	protected function getShardIdForSelect($type, $primaryKey)
	{
		if(is_string($primaryKey))
			$primaryKey = crc32($primaryKey);

		$primaryKey = (int)$primaryKey/$this->getIdStep();

		return ((int)$primaryKey) % $this->count();
	}

	/**
	 * Get storage component for update model.
	 * As example, rule (int)$primaryKey % $this->count().
	 *
	 * @return IDataStorage
	 */
	protected function getShardForUpdate($type, $primaryKey)
	{
		return $this->getShardIdForSelect($type, $primaryKey);
	}
}
