<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     * @return Extendix_CartValidationRules_Model_Observer
     */
    public function processQuoteRules(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
//
//        /** @var Mage_SalesRule_Model_Rule $rule */
//        $rule = Mage::getModel('salesrule/rule')->load(161141);
//
//        if($rule->getConditions()->validate($quote)) {
//            $quote->addErrorInfo(
//                'color_restriction',
//                'Extendix_CartRestrictions',
//                'color_restriction_in_cart',
//                'You are not allowed to checkout same time when you have products with color Blue and Indigo in cart!'
//            );
//        }
//
//        /** @var Mage_SalesRule_Model_Rule $rule2 */
//        $rule2 = Mage::getModel('salesrule/rule')->load(161142);
//
//        if($rule2->getConditions()->validate($quote)) {
//            $quote->addErrorInfo(
//                'sku_restriction',
//                'Extendix_CartRestrictions',
//                'color_restriction_in_cart',
//                'Skus acj003 and acj004 not allowed in cart in cart!'
//            );
//        }

        /** @var Extendix_CartRestrictions_Model_Rule $restrictionRule */
        $restrictionRule = Mage::getModel('extendix_cartrestrictions/rule')->load(1);

        Varien_Profiler::start('extendix_validation');

        if($restrictionRule->getConditions()->validate($this->_getAddress($quote))) {
            $quote->addErrorInfo(
                'sku_restriction',
                'Extendix_CartRestrictions',
                'extendix_restriction_in_cart',
                'Extendix restriction test'
            );
        }


        Varien_Profiler::stop('extendix_validation');

        return $this;
    }

    /**
     * Get address object which can be used for discount calculation
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

}