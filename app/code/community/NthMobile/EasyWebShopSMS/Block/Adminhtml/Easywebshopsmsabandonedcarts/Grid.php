<?php

/**
 * Class NthMobile_EasyWebShopSMS_Block_Adminhtml_Abandonedcarts_Grid
 */
class NthMobile_EasyWebShopSMS_Block_Adminhtml_Easywebshopsmsabandonedcarts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * @return NthMobile_EasyWebShopSMS_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel('easywebshopsms/config');
    }


    public function __construct()
    {
        parent::__construct();
        $this->setId('easywebshopsmsabandonedcartsGrid');
        $this->setDefaultSort('cart_updated_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return mixed
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {

        // Delay
        $delay = $this->getConfig()->getNotifDelay();

        // Default store and website
        $defaults = $this->_getDefaultStoreAndWebsite();

        // Store and website from the multistore switcher
        $store = $this->_getStore();
        if ($storeId = $store->getId())
        {
            $defaults = array(
                $storeId,
                Mage::getModel('core/store')->load($storeId)->getWebsiteId()
            );
        }

        $collection = Mage::getModel('NthMobile_EasyWebShopSMS_Model_Abandonedcart_Collection')->getCollection($delay, $defaults[0], $defaults[1]);
        //Mage::log((string) $collection->getSelect());
        // Group by to have a nice grid
        if (Mage::helper('catalog/product_flat')->isEnabled()) {
            $collection->getSelect()->columns(
                array(
                    'product_ids'   =>  'GROUP_CONCAT(e.entity_id)',
                    'product_names'   =>  'GROUP_CONCAT(catalog_flat.name)',
                    'product_prices'   =>  'SUM(catalog_flat.price)'
                )
            );
        } else {
            $collection->getSelect()->columns(
                array(
                    'product_ids'   =>  'GROUP_CONCAT(e.entity_id)',
                    'product_names'   =>  'GROUP_CONCAT(catalog_name.value)',
                    'product_prices'   =>  'SUM(catalog_price.value)'
                )
            );
        }
        $collection->getSelect()->group('customer_mobile_number_attribute.value');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_mobile_number', array(
            'header' => Mage::helper('easywebshopsms')->__('Customer Mobile'),
            'index' => 'customer_mobile_number',
            'filter_index' => 'customer_mobile_number_attribute.value',
            'filter_condition_callback'  =>  array($this, 'filterCallback')
        ));

        $this->addColumn('customer_firstname', array(
            'header' => Mage::helper('easywebshopsms')->__('Customer Firstname'),
            'index' => 'customer_firstname',
            'filter_condition_callback'  =>  array($this, 'filterCallback')
        ));

        $this->addColumn('customer_lastname', array(
            'header' => Mage::helper('easywebshopsms')->__('Customer Lastname'),
            'index' => 'customer_lastname',
            'filter_condition_callback'  =>  array($this, 'filterCallback')
        ));

        $this->addColumn('product_ids', array(
            'header' => Mage::helper('easywebshopsms')->__('Product Ids'),
            'index' => 'product_ids',
            'filter_index'  =>  "e.entity_id",
            'filter_condition_callback'  =>  array($this, 'filterEqualCallback')
        ));

        $this->addColumn('product_names', array(
            'header' => Mage::helper('easywebshopsms')->__('Product Names'),
            'index' => 'product_names',
            'filter_index'  =>  (Mage::helper('catalog/product_flat')->isEnabled() ? "catalog_flat.name" : "catalog_name.value"),
            'filter_condition_callback'  =>  array($this, 'filterEqualCallback')
        ));

        $this->addColumn('product_prices', array(
            'header' => Mage::helper('easywebshopsms')->__('Cart Total'),
            'index' => 'product_prices',
            'filter'    => false,
            'type'  => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
        ));

        // Output format for the start and end dates
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $this->addColumn('cart_updated_at', array(
            'header' => Mage::helper('easywebshopsms')->__('Cart Updated At'),
            'index' => 'cart_updated_at',
            'type'  => 'datetime',
            'format' => $outputFormat,
            'default' => ' -- ',
            'filter_index'  =>  'quote_table.updated_at',
            'filter_condition_callback' =>  array($this, 'filterDateCallback')
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_mobile_number');
        $this->getMassactionBlock()->setFormFieldName('easywebshopsmsabandonedcarts');

        $this->getMassactionBlock()->addItem('notify', array(
            'label' => Mage::helper('easywebshopsms')->__('Send notification'),
            'url' => $this->getUrl('*/*/notify', array('store'  =>  $this->getRequest()->getParam('store', 0)))
        ));

        return $this;
    }

    /**
     * @return array
     */
    protected function _getDefaultStoreAndWebsite()
    {
        foreach (Mage::app()->getWebsites() as $website) {
            // Get the website id
            $websiteId = $website->getWebsiteId();
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {

                    // Get the store id
                    $storeId = $store->getStoreId();
                    break 3;
                }
            }
        }
        return array($storeId, $websiteId);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('current' => true, 'store'  =>  $this->getRequest()->getParam('store', 0)));
    }

    /**
     * @param $collection
     * @param $column
     */
    public function filterCallback($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        if(!empty($value)) {
            $collection->getSelect()->where("$field like ?", '%' . $value . '%');
        }
    }

    /**
     * @param $collection
     * @param $column
     */
    public function filterEqualCallback($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        if(!empty($value)) {
            $collection->getSelect()->where("$field = ?", $value);
        }
    }

    /**
     * @param $collection
     * @param $column
     */
    public function filterDateCallback($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        if(!empty($value)) {
            $where = "";
            if(!empty($value['from'])){
                $where .= "$field > '" . $value['from']->toString('Y-MM-dd HH:mm:ss') . "'";
            }
            if(!empty($value['to'])){
                if(!empty($where)) {
                    $where .= " AND ";
                }
                $where .= "$field < '" . $value['to']->toString('Y-MM-dd HH:mm:ss') . "'";
            }
            $collection->getSelect()->where($where);
        }
    }

}
