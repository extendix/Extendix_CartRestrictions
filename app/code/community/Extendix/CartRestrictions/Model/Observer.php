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
        if ($this->_getConfigHelper()->isActive()) {
            $this->_getProcessModel()->processRules($observer->getEvent()->getQuote());
        }

        return $this;
    }

    /**
     * @return Extendix_CartRestrictions_Model_Process
     */
    protected function _getProcessModel()
    {
        return Mage::getModel('extendix_cartrestrictions/process');
    }

    /**
     * @return Extendix_CartRestrictions_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('extendix_cartrestrictions/config');
    }

}