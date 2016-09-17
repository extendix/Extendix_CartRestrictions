<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Rule
    extends Mage_Rule_Model_Abstract
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'extendix_cartrestrictions_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = array();

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('extendix_cartrestrictions/rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Extendix_CartRestrictions_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('extendix_cartrestrictions/rule_condition_combine');
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return new Mage_Rule_Model_Action_Collection();
    }

    /**
     * Get cart restriction rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Check cached validation result for specific address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   bool $validationResult
     * @return  Extendix_CartRestrictions_Model_Rule
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  string
     */
    private function _getAddressId($address) {
        if($address instanceof Mage_Sales_Model_Quote_Address) {
            return $address->getId();
        }
        return $address;
    }

    /**
     * Set if not yet and retrieve rule store messages
     *
     * @return array
     */
    public function getStoreMessages()
    {
        if (!$this->hasStoreMessages()) {
            $messages = $this->_getResource()->getStoreMessages($this->getId());
            $this->setStoreMessages($messages);
        }

        return $this->_getData('store_messages');
    }

    /**
     * Get Rule message by specified store
     *
     * @param Mage_Core_Model_Store|int|bool|null $store
     *
     * @return string|bool
     */
    public function getStoreMessage($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        $messages = (array)$this->getStoreMessages();

        if (isset($messages[$storeId])) {
            return $messages[$storeId];
        } elseif (isset($messages[0]) && $messages[0]) {
            return $messages[0];
        }

        return false;
    }

    /**
     * Initialize rule model data from array.
     * Set store messages if applicable.
     *
     * @param array $data
     *
     * @return Extendix_CartRestrictions_Model_Rule
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_messages'])) {
            $this->setStoreMessages($data['store_messages']);
        }

        return $this;
    }

    /**
     * @param Varien_Object $object
     * @return array|bool
     */
    public function validateData(Varien_Object $object)
    {
        $validationMessages = parent::validateData($object);
        $validationMessages = true === $validationMessages ? array() : $validationMessages;

        /** @var array $ruleData */
        $ruleData = $object->getData('rule');

        /** Checking if conditions are empty. It's in customers interest if we don't save such conditions! */
        if (!is_array($ruleData['conditions']) || 1 >= count($ruleData['conditions'])) {
            $validationMessages[] = Mage::helper('extendix_cartrestrictions')
                ->__('Conditions are empty. You are not allowed to save rule with empty condition because this condition will be always valid and customers would be never able to reach the checkout page! In practice nobody would be able to buy from your shop!');
        }

        /**
         * Check if we have default store message. In case we don't have any then we would
         * not show any messages to the customer but the checkout button would not be visible.
         * That's dangerous!!!
         */
        $storeMessages = $object->getData('store_messages');
        if (!is_array($storeMessages) || empty($storeMessages[0])) {
            $validationMessages[] = Mage::helper('extendix_cartrestrictions')
                ->__('Having default Rule message is mandatory. Otherwise the customer would not be able to get any feedback why he/she can\'t complete the checkout!');
        }

        return !empty($validationMessages) ? $validationMessages : true;
    }

}