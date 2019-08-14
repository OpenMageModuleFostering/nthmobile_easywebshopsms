<?php

class NthMobile_EasyWebShopSMS_Model_Bulkclient
{
    private $client;

    public function __construct()
    {
        $this->client = new \Samurai\BulkSms\BulkSms(
            Mage::getStoreConfig('easywebshopsms/api_connection/api_uri'),
            Mage::getStoreConfig('easywebshopsms/api_connection/api_user'),
            Mage::getStoreConfig('easywebshopsms/api_connection/api_pass'),
            Mage::getStoreConfig('easywebshopsms/api_connection/api_port'),
            '',
            [
                'timeout' => 10
            ]
        );

        $this->client->setLogger(NthMobile_EasyWebShopSMS_Model_Logger_Factory::getInstance());

        return $this;
    }

    /**
     * @return \Samurai\BulkSms\BulkSms
     */
    public function getClient()
    {
        return $this->client;
    }
}