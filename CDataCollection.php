<?php
/**
 * Created by JetBrains PhpStorm.
 * User: php2
 * Date: 13.12.10
 * Time: 14:24
 * To change this template use File | Settings | File Templates.
 */
 
class CDataCollection extends CTypedList
{
	public function __construct()
	{
		parent::__construct("IStoragedModel");
	}
}
