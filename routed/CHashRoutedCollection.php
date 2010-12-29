<?php
/**
 * File contains class CHashRoutedCollection
 */
 
/**
 * Class CHashRoutedCollection 
 *
 * @author php2 <php2@gmail.com>
 * @version 0.1
 * @package datamapper.routed
 * @since 0.1
 */
class CHashRoutedCollection extends CRoutedCollection
{
	private $_idStep = 1;

	public function getIdStep()
	{
		return (int)$this->_idStep;
	}

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
