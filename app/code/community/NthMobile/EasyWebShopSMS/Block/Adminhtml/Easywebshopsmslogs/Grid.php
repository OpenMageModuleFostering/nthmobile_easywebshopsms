<?php

class NthMobile_EasyWebShopSMS_Block_Adminhtml_Easywebshopsmslogs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {

        parent::__construct();
        $this->setId('easywebshopsmslogsGrid');
        // This is the primary key of the database
        $this->setDefaultSort('easywebshopsms_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('easywebshopsms/easywebshopsms')->getCollection();
        $fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
        $ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
        $collection->getSelect()
            ->join(array('ce1' => 'customer_entity_varchar'), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'))
            ->where('ce1.attribute_id='.$fn->getAttributeId())
            ->join(array('ce2' => 'customer_entity_varchar'), 'ce2.entity_id=main_table.customer_id', array('lastname' => 'value'))
            ->where('ce2.attribute_id='.$ln->getAttributeId())
            ->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('easywebshopsms')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
            'filter_condition_callback'  =>  array($this, 'filterEqualCallback')
        ));


        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('easywebshopsms')->__('Customer'),
            'align'     =>'left',
            'width'     => '250px',
            'index'     => 'customer_name',
            'filter_index'     => "CONCAT(`ce1`.`value`, ' ',`ce2`.`value`)",
            'filter_condition_callback'  =>  array($this, 'filterCallback')
        ));

        $this->addColumn('event_name', array(
            'header'    => Mage::helper('easywebshopsms')->__('Event'),
            'align'     => 'left',
            'width'     => '150px',
            'type'      => 'varchar',
            'default'   => '--',
            'index'     => 'event_name',
            'filter_condition_callback'  =>  array($this, 'filterCallback')
        ));

        $this->addColumn('log_time', array(
            'header'    => Mage::helper('easywebshopsms')->__('Log Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'log_time',
            'filter_condition_callback' =>  array($this, 'filterDateCallback')
        ));


        $this->addColumn('sms_origin', array(

            'header'    => Mage::helper('easywebshopsms')->__('SMS Origin'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'sms_origin',
            'type'      => 'varchar',
            'filter_condition_callback'  =>  array($this, 'filterCallback')

        ));
        $this->addColumn('mobile_number', array(

            'header'    => Mage::helper('easywebshopsms')->__('Mobile Number'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'mobile_number',
            'type'      => 'varchar',
            'filter_condition_callback'  =>  array($this, 'filterCallback')

        ));
        $this->addColumn('message_text', array(

            'header'    => Mage::helper('easywebshopsms')->__('Message Text'),
            'align'     => 'left',
            'index'     => 'message_text',
            'type'      => 'varchar',
            'filter_condition_callback'  =>  array($this, 'filterCallback')

        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
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
