<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Shopping Cart Rule data model
 *
 * @method Mage_SalesRule_Model_Resource_Rule _getResource()
 * @method Mage_SalesRule_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Mage_SalesRule_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Mage_SalesRule_Model_Rule setDescription(string $value)
 * @method string getFromDate()
 * @method Mage_SalesRule_Model_Rule setFromDate(string $value)
 * @method string getToDate()
 * @method Mage_SalesRule_Model_Rule setToDate(string $value)
 * @method int getUsesPerCustomer()
 * @method Mage_SalesRule_Model_Rule setUsesPerCustomer(int $value)
 * @method int getUsesPerCoupon()
 * @method Mage_SalesRule_Model_Rule setCustomerGroupIds(string $value)
 * @method int getIsActive()
 * @method Mage_SalesRule_Model_Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Mage_SalesRule_Model_Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method Mage_SalesRule_Model_Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method Mage_SalesRule_Model_Rule setStopRulesProcessing(int $value)
 * @method int getIsAdvanced()
 * @method Mage_SalesRule_Model_Rule setIsAdvanced(int $value)
 * @method string getProductIds()
 * @method Mage_SalesRule_Model_Rule setProductIds(string $value)
 * @method int getSortOrder()
 * @method Mage_SalesRule_Model_Rule setSortOrder(int $value)
 * @method string getSimpleAction()
 * @method Mage_SalesRule_Model_Rule setSimpleAction(string $value)
 * @method float getDiscountAmount()
 * @method Mage_SalesRule_Model_Rule setDiscountAmount(float $value)
 * @method float getDiscountQty()
 * @method Mage_SalesRule_Model_Rule setDiscountQty(float $value)
 * @method int getDiscountStep()
 * @method Mage_SalesRule_Model_Rule setDiscountStep(int $value)
 * @method int getSimpleFreeShipping()
 * @method Mage_SalesRule_Model_Rule setSimpleFreeShipping(int $value)
 * @method int getApplyToShipping()
 * @method Mage_SalesRule_Model_Rule setApplyToShipping(int $value)
 * @method int getTimesUsed()
 * @method Mage_SalesRule_Model_Rule setTimesUsed(int $value)
 * @method int getIsRss()
 * @method Mage_SalesRule_Model_Rule setIsRss(int $value)
 * @method string getWebsiteIds()
 * @method Mage_SalesRule_Model_Rule setWebsiteIds(string $value)
 * @method int getCouponType()
 * @method Mage_SalesRule_Model_Rule setCouponType(int $value)
 * @method int getUseAutoGeneration()
 * @method Mage_SalesRule_Model_Rule setUseAutoGeneration(int $value)
 * @method string getCouponCode()
 * @method Mage_SalesRule_Model_Rule setCouponCode(string $value)
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @return Mage_SalesRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('extendix_cartrestrictions/rule_condition_combine');
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function getActionsInstance()
    {
        return Mage::getModel('extendix_cartrestrictions/rule_condition_product_combine');
    }

    /**
     * Get sales rule customer group Ids
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
     * @return  Mage_SalesRule_Model_Rule
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

}