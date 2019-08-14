<?php

/**
 * @author Nikola Bakos <nikola.bakos@nth.ch>
 *
 * Paths follow the following pattern:
 * /:module/:controller/:action
 *
 * :module is what matches package/module/config.xml
 *         config.frontend.routers.{module}.args.frontName value,
 *         and in this case 'easywebshopsms'
 *
 * :controller defaults to index
 * :action defaults to index
 *
 */
class NthMobile_EasyWebShopSMS_TestController extends Mage_Core_Controller_Front_Action
{

    /**
     * paths: /easywebshopsms/
     *        /easywebshopsms/test
     *        /easywebshopsms/test/log
     *
     * @return void
     */
    public function logAction()
    {
        Mage::log(" - - - - -  - - - - -  - - - - -  - - - - -  - - - - - " , Zend_Log::DEBUG, 'test.log');
        Mage::log("log Action: " . file_get_contents('php://input') , Zend_Log::DEBUG, 'test.log');

        $this->getResponse()->setBody("{\"result\":\"OK\"}");
        $this->getResponse()->setHeader('Content-type', 'application/json');
    }


}