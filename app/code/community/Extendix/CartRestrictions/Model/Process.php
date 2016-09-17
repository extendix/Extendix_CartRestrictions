<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Process
{

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function processRules(Mage_Sales_Model_Quote $quote)
    {
        $validationPassed = true;

        if (!$this->_isProcessingAllowed()) {
            return true;
        }

        Varien_Profiler::start('extendix_cartrestrictions_process_validations');

        $restrictionRuleCollection = $this->_getFilteredRestrictionRuleCollection();

        /** @var Extendix_CartRestrictions_Model_Rule $rule */
        foreach ($restrictionRuleCollection as $rule) {
            Varien_Profiler::start('extendix_cartrestrictions_validate_rule_id_' . $rule->getId());

            if($rule->getConditions()->validate($this->_getAddress($quote))) {
                $quote->addErrorInfo(
                    'extendix_cartrestrictions_validation',
                    'Extendix_CartRestrictions',
                    'extendix_cartrestrictions_validate_rule_id_' . $rule->getId(),
                    $rule->getStoreMessage()
                );

                $validationPassed = false;
            }

            Varien_Profiler::stop('extendix_cartrestrictions_validate_rule_id_' . $rule->getId());
        }

        Varien_Profiler::stop('extendix_cartrestrictions_process_validations');

        return $validationPassed;
    }

    /**
     * Get address object which can be used for cart restrictions validation
     *
     * @param $quote Mage_Sales_Model_Quote
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddress(Mage_Sales_Model_Quote $quote)
    {
        if ($quote->getItemVirtualQty() > 0) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        return $address;
    }

    /**
     * @return Extendix_CartRestrictions_Model_Resource_Rule_Collection
     */
    protected function _getFilteredRestrictionRuleCollection()
    {
        /** @var Extendix_CartRestrictions_Model_Resource_Rule_Collection $restrictionRuleCollection */
        $restrictionRuleCollection = Mage::getModel('extendix_cartrestrictions/rule')->getCollection();
        return $restrictionRuleCollection->setValidationFilter($this->_getCurrentWebsiteId(), $this->_getCurrentCustomerGroupId(), null);
    }

    /**
     * @return int
     */
    protected function _getCurrentWebsiteId()
    {
        return Mage::app()->getWebsite()->getId();
    }

    /**
     * @return int
     */
    protected function _getCurrentCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * We don't want to add overhead for all Magento pages because this validation make
     * sense only for cart and checkout page. We do the validation when we load the quote
     * and the quote is loaded at every page because we load th quote when we display the cart
     * in header.
     *
     * So far we allow this validation only for Checkout Controller
     *
     * @return bool
     */
    protected function _isProcessingAllowed()
    {
        return 'Mage_Checkout' === Mage::app()->getRequest()->getControllerModule();
    }

}