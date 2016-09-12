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

}