<?php

$installer = $this;
$installer->startSetup();

// Add a notification column to the sales_flat_quote table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('sales/quote'), 'easywebshopsms_abandoned_sale_notified', 'int(10) not null default 0'
	);
$installer
	->getConnection()
	->addColumn(
		$this->getTable('sales/quote'), 'easywebshopsms_abandoned_notified', 'int(10) not null default 0'
	);

$installer->endSetup();
