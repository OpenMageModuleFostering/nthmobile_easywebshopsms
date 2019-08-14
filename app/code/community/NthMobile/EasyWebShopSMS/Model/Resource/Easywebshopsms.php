<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 30.5.2016.
 * Time: 14:39
 */
class NthMobile_EasyWebShopSMS_Model_Resource_Easywebshopsms extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('easywebshopsms/easywebshopsms','id');
    }
}