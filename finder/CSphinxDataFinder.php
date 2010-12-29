<?php
/**
 * File contains class CSphinxDataFinder
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Class CSphinxDataFinder
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
 */
class CSphinxDataFinder extends CAbstractDataFinder implements IDataFinder
{
	private $_index_list;
	private $_sphinx;
	/**
	 * Constructor
	 * @param string|array $indexList
	 */
	public function __construct($indexList)
	{
		if(is_array($indexList))
			$indexList = join(" ", $indexList);

		$this->_index_list = (string)$indexList;
	}
	/**
	 * Find in index by criteria, and return models list.
	 *
	 * @param string $type
	 * @param IDataFinder $criteria
	 * @return IModelStorage[]
	 * @required set storage
	 */
	public function findModels($type, IDataFinderCriteria $criteria)
	{
		$list = $this->findModelsId($criteria);
		$criteria->matchMode = ESphinxCriteria::MATCH_FULLSCAN;
		return $this->getStorage()->findAllByPk($type, $list);
	}
	/**
	 * Find in index by criteria, and return models id list.
	 *
	 * @param IDataFinder $criteria
	 * @return array model id list
	 */
	public function findModelsId(IDataFinderCriteria $criteria)
	{
		$query = new ESphinxQuery("", $this->getIndexList(), $criteria);
		$result = $this->getConnection()->executeQuery($query);
		return $result->getAttributeList("id");
	}
	/**
	 * Get index list as string, using by finder.
	 * 
	 * @return string
	 */
	public function getIndexList()
	{
		return $this->_index_list;
	}
	/**
	 * Get setted sphinx connection
	 * 
	 * @return ESphinxConnection
	 */
	public function getConnection()
	{
		return $this->_sphinx;
	}
	/**
	 * Set sphinx connection.
	 * 
	 * @param ESphinxConnection|string $connection ESphinxConnection connection object or id of Yii component
	 * @throws CStorageException if connection is not instance of ESphinxConnection or not found
	 */
	public function setConnection( $connection )
	{
		if(is_string($connection))
		{
			$connection = Yii::app()->getComponent($connection);
		}

		if($connection instanceof ESphinxConnection)
			$this->_sphinx = $connection;
		else
			throw new CStorageException(Yii::t("db.finder", "\$connection is not instance of ESphinxConnection"));
	}
}