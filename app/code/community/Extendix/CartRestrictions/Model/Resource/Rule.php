<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Resource_Rule
    extends Mage_Rule_Model_Resource_Abstract
{

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'extendix_cartrestrictions/website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        ),
        'customer_group' => array(
            'associations_table' => 'extendix_cartrestrictions/customer_group',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'customer_group_id'
        )
    );

    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('extendix_cartrestrictions/rule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Extendix_CartRestrictions_Model_Resource_Rule
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('customer_group_ids', (array)$this->getCustomerGroupIds($object->getId()));
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Bind cart restriction rule to customer group(s) and website(s).
     * Save rule's associated store messages.
     * Save product attributes used in rule.
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Extendix_CartRestrictions_Model_Resource_Rule
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasStoreMessages()) {
            $this->saveStoreMessages($object->getId(), $object->getStoreMessages());
        }

        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        // Save product attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->getProductAttributes(serialize($object->getConditions()->asArray()))
        );
        if (count($ruleProductAttributes)) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return codes of all product attributes currently used in cart restrictions rules for specified customer group and website
     *
     * @return mixed
     */
    public function getActiveAttributes()
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('a' => $this->getTable('extendix_cartrestrictions/product_attribute')),
                new Zend_Db_Expr('DISTINCT ea.attribute_code'))
            ->joinInner(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = a.attribute_id', array());

        return $read->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions of rule
     *
     * @param Extendix_CartRestrictions_Model_Rule $rule
     * @param mixed $attributes
     * @return Extendix_CartRestrictions_Model_Resource_Rule
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('extendix_cartrestrictions/product_attribute'), array('rule_id=?' => $rule->getId()));

        //Getting attribute IDs for attribute codes
        $attributeIds = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('a' => $this->getTable('eav/attribute')), array('a.attribute_id'))
            ->where('a.attribute_code IN (?)', array($attributes));
        $attributesFound = $this->_getReadAdapter()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = array();
            foreach ($rule->getCustomerGroupIds() as $customerGroupId) {
                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = array (
                            'rule_id'           => $rule->getId(),
                            'website_id'        => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'attribute_id'      => $attribute
                        );
                    }
                }
            }
            $write->insertMultiple($this->getTable('extendix_cartrestrictions/product_attribute'), $data);
        }

        return $this;
    }

    /**
     *
     * Collect all product attributes used in serialized rule's condition
     *
     * @param string $serializedString
     *
     * @return array
     */
    public function getProductAttributes($serializedString)
    {
        $result = array();
        if (preg_match_all('~s:48:"extendix_cartrestrictions/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString, $matches)){
            foreach ($matches[1] as $offset => $attributeCode) {
                $result[] = $attributeCode;
            }
        }

        return $result;
    }

    /**
     * Get all existing rule messages
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreMessages($ruleId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('extendix_cartrestrictions/message'), array('store_id', 'message'))
            ->where('rule_id = :rule_id');
        return $this->_getReadAdapter()->fetchPairs($select, array(':rule_id' => $ruleId));
    }

    /**
     * Save rule messages for different store views
     *
     * @param int $ruleId
     * @param array $messages
     *
     * @return Extendix_CartRestrictions_Model_Resource_Rule
     *
     * @throws Exception
     */
    public function saveStoreMessages($ruleId, array $messages)
    {
        $deleteByStoreIds = array();
        $table   = $this->getTable('extendix_cartrestrictions/message');
        $adapter = $this->_getWriteAdapter();

        $data    = array();
        foreach ($messages as $storeId => $message) {
            if (Mage::helper('core/string')->strlen($message)) {
                $data[] = array('rule_id' => $ruleId, 'store_id' => $storeId, 'message' => $message);
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $adapter->beginTransaction();
        try {
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $table,
                    $data,
                    array('message')
                );
            }

            if (!empty($deleteByStoreIds)) {
                $adapter->delete($table, array(
                    'rule_id=?'       => $ruleId,
                    'store_id IN (?)' => $deleteByStoreIds
                ));
            }
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;

        }
        $adapter->commit();

        return $this;
    }

}
