<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 23.5.2016.
 * Time: 11:31
 */
$installer = $this;

$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute("customer", "mobile_number",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Mobile Number",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => "Mobile Number"

));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "mobile_number");


$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'mobile_number',
    '999'  //sort_order
);

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="checkout_register";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
$used_in_forms[]="adminhtml_checkout";
$attribute->setData("used_in_forms", $used_in_forms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100)
;
$attribute->save();




$table = $installer->getConnection()
    ->newTable($installer->getTable('easywebshopsms'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
    ), 'Customer Id')
    ->addColumn('event_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
    ), 'Event Name')
    ->addColumn('log_time', Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
        'nullable'  => false,
    ), 'Log Time')
    ->addColumn('sms_origin', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,
    ), 'SMS Origin')
    ->addColumn('mobile_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,
    ), 'Mobile Number')    
    ->addColumn('message_text', Varien_Db_Ddl_Table::TYPE_VARCHAR, 500, array(
        'nullable'  => true,
    ), 'Message Text')
    ->addColumn('sms_status_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
    ), 'Sms Status Code')
    ->addColumn('sms_status_message', Varien_Db_Ddl_Table::TYPE_VARCHAR, 200, array(
        'nullable'  => true,
    ), 'Sms Status Message')
    ->addColumn('sms_sent', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
    ), 'Sms Sent');
$installer->getConnection()->createTable($table);


$installer->endSetup();