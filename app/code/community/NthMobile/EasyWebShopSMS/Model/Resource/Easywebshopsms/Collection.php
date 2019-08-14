<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 30.5.2016.
 * Time: 14:41
 */
class NthMobile_EasyWebShopSMS_Model_Resource_Easywebshopsms_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('easywebshopsms/easywebshopsms');
    }
}