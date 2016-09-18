<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'extendix_cartrestrictions/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('extendix_cartrestrictions/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
        'default'   => null
        ), 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
        'default'   => null
        ), 'To Date')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/rule', array('is_active', 'to_date', 'from_date')),
        array('is_active', 'to_date', 'from_date'))
    ->setComment('Extendix Cart Restrictions Rules');
$installer->getConnection()->createTable($table);

/**
 * Create table 'extendix_cartrestrictions/product_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('extendix_cartrestrictions/product_attribute'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/product_attribute', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/product_attribute', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/product_attribute', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/product_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/product_attribute', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/product_attribute', 'rule_id', 'extendix_cartrestrictions/rule', 'rule_id'),
        'rule_id', $installer->getTable('extendix_cartrestrictions/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/product_attribute', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Extendix Cart Restrictions Rules Product Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'extendix_cartrestrictions/website'
 */
$table = $installer->getConnection()->newTable($installer->getTable('extendix_cartrestrictions/website'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
        'Rule Id'
    )
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
        'Website Id'
    )
    ->addIndex(
        $installer->getIdxName('extendix_cartrestrictions/website', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('extendix_cartrestrictions/website', array('website_id')),
        array('website_id')
    )
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/website', 'rule_id', 'extendix_cartrestrictions/rule', 'rule_id'),
        'rule_id', $installer->getTable('extendix_cartrestrictions/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/website', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Extendix Cart Restrictions Rules To Websites Relations');
$installer->getConnection()->createTable($table);


/**
 * Create table 'extendix_cartrestrictions/customer_group'
 */
$table = $installer->getConnection()->newTable($installer->getTable('extendix_cartrestrictions/customer_group'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
        'Rule Id'
    )
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
        'Customer Group Id'
    )
    ->addIndex(
        $installer->getIdxName('extendix_cartrestrictions/customer_group', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('extendix_cartrestrictions/customer_group', array('customer_group_id')),
        array('customer_group_id')
    )
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/customer_group', 'rule_id', 'extendix_cartrestrictions/rule', 'rule_id'),
        'rule_id', $installer->getTable('extendix_cartrestrictions/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('extendix_cartrestrictions/customer_group', 'customer_group_id',
            'customer/customer_group', 'customer_group_id'
        ),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Extendix Cart Restrictions Rules To Customer Groups Relations');
$installer->getConnection()->createTable($table);

/**
 * Create table 'extendix_cartrestrictions/message'
 *
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('extendix_cartrestrictions/message'))
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Message Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Rule Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Store Id')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Message')
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/message', array('rule_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('rule_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/message', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('extendix_cartrestrictions/message', array('rule_id')),
        array('rule_id'))
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/message', 'rule_id', 'extendix_cartrestrictions/rule', 'rule_id'),
        'rule_id', $installer->getTable('extendix_cartrestrictions/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('extendix_cartrestrictions/message', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Extendix Cart Restrictions Rules Messages');
$installer->getConnection()->createTable($table);

$installer->endSetup();