<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 23.5.2016.
 * Time: 11:31
 */
$installer = $this;
$installer->startSetup();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//attr1
$installer->addAttribute('customer','mobile_number', array(
    'type'      => 'varchar',
    'label'     => 'Mobile Number',
    'input'     => 'text',
    'position'  => 120,
    'required'  => false,
    'is_system' => 0,
));

$attribute1 = Mage::getSingleton('eav/config')->getAttribute('customer', 'mobile_number');

//
$attribute1->setData('used_in_forms', array(
    'adminhtml_customer',
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
));

//
$attribute1->setData('is_user_defined', 0);

//
$attribute1->save();

Mage::log(__FILE__ . 'Update installed.');
$installer->endSetup();