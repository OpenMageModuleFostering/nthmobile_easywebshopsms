<?php

class NthMobile_EasyWebShopSMS_Model_Observer
{

    public static $lastExecutionTime; //to avoid multiple SMS if status was changed more than one time per 2 second

    private $skipMultipleSendSmsPeriod = 5; //to avoid multiple SMS if status was changed more than one time per 5 second

    public function _construct()
    {
        if (!self::$lastExecutionTime)
            self::$lastExecutionTime = time();
    }

    public function autoload()
    {

        if(class_exists('\Samurai\BulkSms\Client', false)) {
            return;
        }
        if(file_exists(__DIR__ . '/../lib/vendor/autoload.php')) {

            require_once(__DIR__ . '/../lib/vendor/autoload.php');
        }
    }

    /**
     * @return NthMobile_EasyWebShopSMS_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel('easywebshopsms/config');
    }

    /**
     * @return NthMobile_EasyWebShopSMS_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('easywebshopsms');
    }

    public function hookToSalesOrderPlaceAfter(Varien_Event_Observer $observer)
    {
        if (time()-self::$lastExecutionTime<=$this->skipMultipleSendSmsPeriod)
            return;

        $eventConfig = "event_place_order";
        $event = $observer->getEvent()->getOrder();

        if($this->getConfig()->isEventActive($eventConfig))
        {
            $helper = $this->getHelper();
            $eventData = $helper->getOrderData($event);
            $text = $this->getConfig()->getEventMessageTemplate($eventConfig);

            $helper->registerEvent($helper->prepareEventData("SalesOrderPlaceAfter", $eventData), $text);

            self::$lastExecutionTime = time();
        }
    }

    public function hookToSalesOrderShipmentPlaceAfter(Varien_Event_Observer $observer)
    {
        if (time()-self::$lastExecutionTime<=$this->skipMultipleSendSmsPeriod)
            return;

        $eventConfig = "event_place_order_shipment";
        $shipment = $observer->getEvent()->getShipment();
        $event = $shipment->getOrder();

        if($this->getConfig()->isEventActive($eventConfig))
        {
            $helper = $this->getHelper();
            $eventData = $helper->getOrderData($event);
            $eventData['shipment']['total_qty'] = intval($shipment->getData('total_qty'));
            $text = $this->getConfig()->getEventMessageTemplate($eventConfig);

            $helper->registerEvent($helper->prepareEventData("SalesOrderShipmentPlaceAfter", $eventData), $text);

            self::$lastExecutionTime = time();
        }
    }

    public function orderStatusHistorySave($observer)
    {

        $config = $this->getConfig();
        if (!$config->isApiEnabled()) return; //do nothing if api is disabled

        $history = $observer->getEvent()->getStatusHistory();

        //only for new status
        if ($history->getId()) {

            $order = $history->getOrder();
            $newStatus =  $order->getData('status');
            $origStatus =  $order->getOrigData('status');

            if (time()-self::$lastExecutionTime<=$this->skipMultipleSendSmsPeriod)
                return;

            //if status has changed run action
            if ($newStatus!=$origStatus) {
                $eventConfig = "event_order_status_".$newStatus;
                $text = $config->getEventMessageTemplate($eventConfig); //get template for new status (if active and exists)

                $helper = $this->getHelper();
                $eventData = $helper->getOrderData($order);

                $helper->registerEvent($helper->prepareEventData("OrderStatusChange $origStatus > $newStatus", $eventData), $text);

                self::$lastExecutionTime = time();

            }
        }
    }


    public function hookToCustomerLogin(Varien_Event_Observer $observer)
    {
        if (time()-self::$lastExecutionTime<=$this->skipMultipleSendSmsPeriod)
            return;
        $eventConfig = "event_customer_login";

        $event = $observer->getEvent();
        if($this->getConfig()->isEventActive($eventConfig))
        {
            $text = $this->getConfig()->getEventMessageTemplate($eventConfig);
            $helper = $this->getHelper();
            $helper->registerEvent($helper->prepareEventData("CustomerLogin",$event->getData()),$text);
            self::$lastExecutionTime = time();
        }
    }

    public function hookToCustomerLogout(Varien_Event_Observer $observer)
    {
        if (time()-self::$lastExecutionTime<=$this->skipMultipleSendSmsPeriod)
            return;
        $eventConfig = "event_customer_logout";
        $event = $observer->getEvent();
        if($this->getConfig()->isEventActive($eventConfig))
        {
            $text = $this->getConfig()->getEventMessageTemplate($eventConfig);
            $helper = $this->getHelper();
            $helper->registerEvent($helper->prepareEventData("CustomerLogout",$event->getData()),$text);
            self::$lastExecutionTime = time();
        }
    }

    public function hookToCustomerRegisterSuccess(Varien_Event_Observer $observer)
    {
        if (time()-self::$lastExecutionTime<=$this->skipMultipleSendSmsPeriod)
            return;
        $eventConfig = "event_customer_register";

        $event = $observer->getEvent()->getData();

        if($this->getConfig()->isEventActive($eventConfig))
        {
            $text = $this->getConfig()->getEventMessageTemplate($eventConfig);
            $helper = $this->getHelper();

            $helper->registerEvent($helper->prepareEventData("CustomerRegisterSuccess", $event),$text);
            self::$lastExecutionTime = time();
        }
    }


}