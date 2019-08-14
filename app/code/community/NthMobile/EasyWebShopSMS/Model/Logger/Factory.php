<?php

use Psr\Log\LoggerInterface;


class NthMobile_EasyWebShopSMS_Model_Logger_Factory
{
    /**
     * Esendex Log File
     */
    const LOG_FILE = 'nthmobile_easywebshopsms.log';

    /**
     * @var LoggerInterface
     */
    static $logger;

    /**
     * @return NthMobile_EasyWebShopSMS_Model_Logger_Logger
     */
    public static function getInstance()
    {
        if (static::$logger) {
            return static::$logger;
        }

        $file   = sprintf('%s/%s', Mage::getBaseDir('log'), static::LOG_FILE);
        $logger = new Zend_Log(new Zend_Log_Writer_Stream($file));

        if (!Mage::getStoreConfig('easywebshopsms/api_connection/debug_mode')) {
            $logger->addFilter(new Zend_Log_Filter_Priority(Zend_Log::CRIT));
        }

        static::$logger = new NthMobile_EasyWebShopSMS_Model_Logger_Logger($logger);
        return static::$logger;
    }
}
