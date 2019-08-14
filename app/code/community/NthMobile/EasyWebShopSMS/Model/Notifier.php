<?php

/**
 * Class NthMobile_EasyWebShopSMS_Model_Notifier
 */
class NthMobile_EasyWebShopSMS_Model_Notifier extends Mage_Core_Model_Abstract
{
	/**
	 * @var array
	 */
	protected $_recipients = array();

	/**
	 * @var array
	 */
	protected $_saleRecipients = array();

	/**
	 * @var string
	 */
	protected $_today = "";

	/**
	 * @var array
	 */
	protected $_customerGroups = array();

	/**
	 * @var
	 */
	protected $_currentStoreId;

	/**
	 * @var
	 */
	protected $_originalStoreId;

	/**
	 * @throws Zend_Date_Exception
	 */
	protected function _setToday()
	{
		// Date handling
		$store = Mage_Core_Model_App::ADMIN_STORE_ID;
		$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
		date_default_timezone_set($timezone);

		// Current date
		$currentdate = date("Ymd");

		$day = (int)substr($currentdate,-2);
		$month = (int)substr($currentdate,4,2);
		$year = (int)substr($currentdate,0,4);

		$date = array(
			'year' => $year,
			'month' => $month,
			'day' => $day,
			'hour' => 23,
			'minute' => 59,
			'second' => 59
		);

		$today = new Zend_Date($date);
		$today->setTimeZone("UTC");

		date_default_timezone_set($timezone);

		$this->_today = $today->toString("Y-MM-dd HH:mm:ss");
	}

	/**
	 * @return string
	 */
	protected function _getToday()
	{
		return $this->_today;
	}

	/**
	 * @return array
	 */
	protected function _getRecipients()
	{
		return $this->_recipients;
	}

	/**
	 * @return array
	 */
	protected function _getSaleRecipients()
	{
		return $this->_saleRecipients;
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

	/**
	 * @param $args
	 */
	public function generateRecipients($args)
	{
		// Customer group check
		if (array_key_exists('customer_group',$args['row'])
			&& !in_array($args['row']['customer_group'],$this->_customerGroups)) {
			return;
		}

		// Test if the customer is already in the array
		if (!array_key_exists($args['row']['customer_mobile_number'], $this->_recipients)) {

			// Create an array of variables to assign to template
			$smsDetails = array();

			// Array that contains the data which will be used inside the template
			$smsDetails['fullname'] = $args['row']['customer_firstname'].' '.$args['row']['customer_lastname'];
			$smsDetails['firstname'] = $args['row']['customer_firstname'];
			$smsDetails['customer_id'] = $args['row']['customer_id'];
			$smsDetails['productname'][] = $args['row']['product_name'];
			$smsDetails['cartprice'][] = Mage::helper('core')->currency(floatval(number_format(floatval($args['row']['product_price']),2)), true, false);
			$smsDetails['cartprice_sum'] = floatval(number_format(floatval($args['row']['product_price']),2));
			// Assign the values to the array of recipients
			$this->_recipients[$args['row']['customer_mobile_number']]['cartId'] = $args['row']['cart_id'];

			$smsDetails['link']	= $this->_generateUrl($this->_currentStoreId);
		} else {

			$smsDetails = $this->_recipients[$args['row']['customer_mobile_number']]['smsDetails'];

			$smsDetails['productname'][] = $args['row']['product_name'];
			$smsDetails['cartprice'][] = Mage::helper('core')->currency(floatval(number_format(floatval($args['row']['product_price']),2)), true, false);
			$smsDetails['cartprice_sum'] += floatval(number_format(floatval($args['row']['product_price']),2));
		}

		//$smsDetails['cartprice_sum'] = Mage::helper('core')->currency($smsDetails['cartprice_sum'], true, false);

		// Assign the array of template variables
		$this->_recipients[$args['row']['customer_mobile_number']]['smsDetails'] = $smsDetails;
		$this->_recipients[$args['row']['customer_mobile_number']]['store_id'] = $this->_currentStoreId;

	}

	/**
	 * @param $args
	 */
	public function generateSaleRecipients($args)
	{

		// Customer group check
		if (array_key_exists('customer_group',$args['row'])
			&& !in_array($args['row']['customer_group'],$this->_customerGroups)) {

			return;
		}

		// Double check if the special from date is set
		if (!array_key_exists('product_special_from_date',$args['row'])
			|| !$args['row']['product_special_from_date']) {

			// If not we use today for the comparison
			$fromDate = $this->_getToday();
		} else {
			$fromDate = $args['row']['product_special_from_date'];
		}

		// Do the same for the special to date
		if (!array_key_exists('product_special_to_date',$args['row'])
			|| !$args['row']['product_special_to_date']) {

			$toDate = $this->_getToday();
		} else {
			$toDate = $args['row']['product_special_to_date'];
		}

		// We need to ensure that the price in cart is higher than the new special price
		// As well as the date comparison in case the sale is over or hasn't started
		if (true || $args['row']['product_price_in_cart'] > 0.00
			&& $args['row']['product_special_price'] > 0.00
			&& ($args['row']['product_price_in_cart'] > $args['row']['product_special_price'])
			&& ($fromDate <= $this->_getToday())
			&& ($toDate >= $this->_getToday())) {

			// Test if the customer is already in the array
			if (!array_key_exists($args['row']['customer_mobile_number'], $this->_saleRecipients)) {

				// Create an array of variables to assign to template
				$smsDetails = array();

				// Array that contains the data which will be used inside the template
				$smsDetails['fullname'] = $args['row']['customer_firstname'].' '.$args['row']['customer_lastname'];
				$smsDetails['firstname'] = $args['row']['customer_firstname'];
				$smsDetails['customer_id'] = $args['row']['customer_id'];
				$smsDetails['productname'][] = $args['row']['product_name'];
				$smsDetails['cartprice'][] = Mage::helper('core')->currency(floatval(number_format(floatval($args['row']['product_price_in_cart']),2)), true, false);
				$smsDetails['specialprice'][] = Mage::helper('core')->currency(floatval(number_format(floatval($args['row']['product_special_price']),2)), true, false);

				// Assign the values to the array of recipients
				$this->_saleRecipients[$args['row']['customer_mobile_number']]['cartId'] = $args['row']['cart_id'];

				$smsDetails['link']	= $this->_generateUrl($this->_currentStoreId);

				$smsDetails['cartprice_sum'] = floatval(number_format(floatval($args['row']['product_price_in_cart']),2));
				$smsDetails['specialprice_sum'] = floatval(number_format(floatval($args['row']['product_special_price']),2));

				// If one product before
				$smsDetails['discount'] = number_format(floatval($args['row']['product_price_in_cart']),2) - number_format(floatval($args['row']['product_special_price']),2);
			} else {
				// We create some extra variables if there is several products in the cart
				$smsDetails = $this->_saleRecipients[$args['row']['customer_mobile_number']]['smsDetails'];
				// Discount amount
				// We add the discount on the second product
				$moreDiscount = number_format(floatval($args['row']['product_price_in_cart']),2) - number_format(floatval($args['row']['product_special_price']),2);
				$smsDetails['discount'] += $moreDiscount;

				$smsDetails['productname'][] = $args['row']['product_name'];
				$smsDetails['cartprice'][] = Mage::helper('core')->currency(floatval(number_format(floatval($args['row']['product_price_in_cart']),2)), true, false);
				$smsDetails['specialprice'][] = Mage::helper('core')->currency(floatval(number_format(floatval($args['row']['product_special_price']),2)), true, false);

				$smsDetails['cartprice_sum'] += floatval(number_format(floatval($args['row']['product_price_in_cart']),2));
				$smsDetails['specialprice_sum'] += floatval(number_format(floatval($args['row']['product_special_price']),2));

			}

			// Assign the array of template variables
			$this->_saleRecipients[$args['row']['customer_mobile_number']]['smsDetails'] = $smsDetails;
			$this->_saleRecipients[$args['row']['customer_mobile_number']]['store_id'] = $this->_currentStoreId;

		}
	}


	/**
	 */
	protected function _sendSaleSMSs()
	{
		$eventConfig = "abandoned_sale_cart_event";
		if(!$this->getConfig()->isEventActive($eventConfig)){
			Mage::getSingleton('adminhtml/session')->addWarning(
				Mage::helper('easywebshopsms')->__(
					'Enable event: "%s" under settings.', Mage::helper('easywebshopsms')->__("Send Abandoned Sale cart SMS")
				)
			);
			return false;
		}


		foreach ($this->_getSaleRecipients() as $mobile => $recipient) {

			// Store Id
			Mage::app()->setCurrentStore($recipient['store_id']);

			$recipient['smsDetails']['mobile_number'] = $mobile;

			$recipient['smsDetails']['discount'] = Mage::helper('core')->currency($recipient['smsDetails']['discount'], true, false);

			$recipient['smsDetails']['cartprice_sum'] = Mage::helper('core')->currency($recipient['smsDetails']['cartprice_sum'], true, false);
			$recipient['smsDetails']['specialprice_sum'] = Mage::helper('core')->currency($recipient['smsDetails']['specialprice_sum'], true, false);


			$quote = Mage::getModel('sales/quote')->load($recipient['cartId']);

				$helper = $this->getHelper();
				$eventData = $helper->getOrderData($quote);
				$eventData['smsDetails'] = $recipient['smsDetails'];
				$eventData['customer_id'] = $eventData['smsDetails']['customer_id'];
				$text = $this->getConfig()->getEventMessageTemplate($eventConfig);

				if($helper->registerEvent($helper->prepareEventData("AbandonedSaleCartEvent", $eventData), $text)){
					// We change the notification attribute
					if(!$this->getConfig()->getDryRun()) {
						$quote->setEasywebshopsmsAbandonedSaleNotified(1);

						$quote->save();
					}
				}

		}
	}


	protected function _sendSMSs()
	{

		$eventConfig = "abandoned_cart_event";

		if(!$this->getConfig()->isEventActive($eventConfig)){
			Mage::getSingleton('adminhtml/session')->addWarning(
				Mage::helper('easywebshopsms')->__(
					'Enable event: "%s" under settings.', Mage::helper('easywebshopsms')->__("Send Abandoned cart SMS")
				)
			);
			return false;
		}

		// Send the emails via a loop
		foreach ($this->_getRecipients() as $mobile => $recipient) {

			// Store Id
			Mage::app()->setCurrentStore($recipient['store_id']);

			$recipient['smsDetails']['mobile_number'] = $mobile;

			$recipient['smsDetails']['discount'] = Mage::helper('core')->currency($recipient['smsDetails']['discount'], true, false);
			$recipient['smsDetails']['cartprice_sum'] = Mage::helper('core')->currency($recipient['smsDetails']['cartprice_sum'], true, false);

			$quote = Mage::getModel('sales/quote')->load($recipient['cartId']);

			$helper = $this->getHelper();
			$eventData = $helper->getOrderData($quote);
			$eventData['smsDetails'] = $recipient['smsDetails'];
			$eventData['customer_id'] = $eventData['smsDetails']['customer_id'];
			$text = $this->getConfig()->getEventMessageTemplate($eventConfig);

			if($helper->registerEvent($helper->prepareEventData("AbandonedCartEvent", $eventData), $text)){
				// We change the notification attribute
				if(!$this->getConfig()->getDryRun()) {
					$quote->setEasywebshopsmsAbandonedNotified(1);

					$quote->save();
				}
			}
		}
	}

	/**
	 * Send notification sms to customer with abandoned cart containing sale products
	 */
	public function sendAbandonedCartsSaleSMS($mobiles = array())
	{

		// Set customer groups
		$this->_customerGroups = $this->_customerGroups ? $this->_customerGroups : $this->getConfig()->getCustomerGroupsLimitation();
		// Original store id
		$this->_originalStoreId = Mage::app()->getStore()->getId();
		try
		{
			if ($this->getConfig()->isApiEnabled()) {
				$this->_setToday();

				// Loop through the stores
				foreach (Mage::app()->getWebsites() as $website) {
					// Get the website id
					$websiteId = $website->getWebsiteId();
					foreach ($website->getGroups() as $group) {
						$stores = $group->getStores();
						foreach ($stores as $store) {

							// Get the store id
							$storeId = $store->getStoreId();
							$this->_currentStoreId = $storeId;

							// Init the store to be able to load the quote and the collections properly
							Mage::app()->init($storeId,'store');

							// Get the collection
							$collection = Mage::getModel('NthMobile_EasyWebShopSMS_Model_Abandonedcart_Collection')->getSalesCollection($storeId, $websiteId, $mobiles);

							$collection->load();

							// Skip the rest of the code if the collection is empty
							if ($collection->getSize() == 0) {
								continue;
							}

							// Call iterator walk method with collection query string and callback method as parameters
							// Has to be used to handle massive collection instead of foreach
							Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'generateSaleRecipients')));
						}
					}
				}
				$this->_sendSaleSMSs();
			}
			Mage::app()->setCurrentStore($this->_originalStoreId);

			return count($this->_getSaleRecipients());
		}
		catch (Exception $e)
		{
			Mage::app()->setCurrentStore($this->_originalStoreId);
			Mage::helper('abandonedcarts')->log(sprintf("%s->Error: %s", __METHOD__, $e->getMessage()));
			return 0;
		}
	}

	/**
	 * Send notification email to customer with abandoned carts after the number of days specified in the config
	 */
	public function sendAbandonedCartsSMS($mobiles = array())
	{

		// Set customer groups
		$this->_customerGroups = $this->_customerGroups ? $this->_customerGroups : $this->getConfig()->getCustomerGroupsLimitation();
		// Original store id
		$this->_originalStoreId = Mage::app()->getStore()->getId();
		try
		{

			if ($this->getConfig()->isApiEnabled()) {

				// Date handling
				$store = Mage_Core_Model_App::ADMIN_STORE_ID;
				$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
				date_default_timezone_set($timezone);

				$delay = $this->getConfig()->getNotifDelay();

				// Loop through the stores
				foreach (Mage::app()->getWebsites() as $website) {
					// Get the website id
					$websiteId = $website->getWebsiteId();
					foreach ($website->getGroups() as $group) {
						$stores = $group->getStores();
						foreach ($stores as $store) {

							// Get the store id
							$storeId = $store->getStoreId();
							$this->_currentStoreId = $storeId;
							// Init the store to be able to load the quote and the collections properly
							Mage::app()->init($storeId, 'store');

							// Get the collection
							$collection = Mage::getModel('NthMobile_EasyWebShopSMS_Model_Abandonedcart_Collection')->getCollection($delay, $storeId, $websiteId, $mobiles);

							$collection->load();

							// Skip the rest of the code if the collection is empty
							if ($collection->getSize() == 0) {
								continue;
							}

							// Call iterator walk method with collection query string and callback method as parameters
							// Has to be used to handle massive collection instead of foreach
							Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'generateRecipients')));
						}
					}
				}
				// Send the sms-es
				$this->_sendSMSs();
			}
			Mage::app()->setCurrentStore($this->_originalStoreId);

			return count($this->_getRecipients());
		}
		catch (Exception $e)
		{
			Mage::app()->setCurrentStore($this->_originalStoreId);
			Mage::helper('easywebshopsms')->log(sprintf("%s->Error: %s", __METHOD__, $e->getMessage()));
			return 0;
		}
	}


	/**
	 * Send notification cron
	 */
	public function sendAbandonedCartsCron(Mage_Cron_Model_Schedule $schedule = null)
	{
		if(!$this->getConfig()->isAutoNotifyEnabled()) {
			return;
		}
		if($this->getConfig()->isAutoNotifyAbandoned()) {
			$this->sendAbandonedCartsSMS();
		}

	}

	/**
	 * Send notification cron
	 */
	public function sendAbandonedCartsSaleCron(Mage_Cron_Model_Schedule $schedule = null)
	{
		if(!$this->getConfig()->isAutoNotifyEnabled()) {
			return;
		}
		if($this->getConfig()->isAutoNotifySale()) {
			$this->sendAbandonedCartsSaleSMS();
		}

	}

	/**
	 * @return mixed|string
	 */
	protected function _generateUrl( $storeId = 0)
	{
			return Mage::getUrl('easywebshopsms',
				array(
					'_store'	=>	$storeId,
					'_nosid'	=>	true,
					'_secure'	=>	true
				)
			);

	}

}