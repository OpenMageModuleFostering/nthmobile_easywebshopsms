<?php

/**
 * Created by PhpStorm.
 * User: Nikola Bakos
 * Date: 5.12.2016.
 * Time: 16:00
 */
class NthMobile_EasyWebShopSMS_Block_Adminhtml_System_Config_Backend_Cron  extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH_easywebshopsms_abandonedcarts_send = 'crontab/jobs/easywebshopsms_abandonedcarts_send/schedule/cron_expr';
    const CRON_STRING_PATH_easywebshopsms_abandonedcarts_sendsale = 'crontab/jobs/easywebshopsms_abandonedcarts_sendsale/schedule/cron_expr';


    protected function _afterSave()
    {
        $expr = $this->getValue();

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH_easywebshopsms_abandonedcarts_send, 'path')
                ->setValue($expr)
                ->setPath(self::CRON_STRING_PATH_easywebshopsms_abandonedcarts_send)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH_easywebshopsms_abandonedcarts_sendsale, 'path')
                ->setValue($expr)
                ->setPath(self::CRON_STRING_PATH_easywebshopsms_abandonedcarts_sendsale)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));

        }
    }


}