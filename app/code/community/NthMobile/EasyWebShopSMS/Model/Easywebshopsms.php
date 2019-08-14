<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 30.5.2016.
 * Time: 14:37
 */
class NthMobile_EasyWebShopSMS_Model_Easywebshopsms extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('easywebshopsms/easywebshopsms');
    }
}