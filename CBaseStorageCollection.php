<?php
/**
 * File contains class CBaseStorageCollection
 */
 
/**
 * Class CBaseStorageCollection 
 *
 * @author php2 <php2@gmail.com>
 * @version
 * @package
 * @since
 */
class CBaseStorageCollection extends CTypedList implements IApplicationComponent
{
	private $_is_initialized;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct("IDataStorage");
	}
	/**
	 * Constructor
	 */
	public function init()
	{
		$this->_is_initialized = true;
		$this->setReadOnly(true);

	}
	/**
	 * @return boolean whether the {@link init()} method has been invoked.
	 */
	public function getIsInitialized()
	{
		return $this->_is_initialized;
	}
	/**
	 * Inserts an item at the specified position.
	 * This method overrides the parent implementation by
	 * checking the item to be inserted is of certain type.
	 * @param integer $index the specified position.
	 * @param mixed $item new item
	 * @throws CException If the index specified exceeds the bound,
	 * the list is read-only or the element is not of the expected type.
	 */
	public function insertAt($index, $item)
	{
		if (is_array($item))
		{
			$item = Yii::createComponent($item);
		}
		elseif (is_string($item))
		{
			/** @var $item IApplicationComponent */
			$item = Yii::app()->getComponent($item);
			$item->init();
		}

		parent::insertAt($index, $item);
	}
	/**
	 * @param array $items
	 * @return void
	 */
	public function setItems(array $items)
	{
		$this->copyFrom($items);
	}
}
