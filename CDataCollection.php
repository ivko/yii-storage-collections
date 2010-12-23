<?php
/**
 * File contains class CDataCollection
 *
 * @author mitallast <mitallast@gmail.com>
 * @link http://github.com/mitallast/yii-srorage-collections
 * @copyright Copyright &copy 2010-2011 mitallast
 * @license MIT license
 */

/**
 * Class CDataCollection
 *
 * @author mitallast <mitallast@gmail.com>
 * @version 0.1
 * @package system
 * @since 0.1
 * @throws CStorageException
 */
class CDataCollection extends CTypedList
{
	public function __construct()
	{
		parent::__construct("IStorageModel");
	}
}
