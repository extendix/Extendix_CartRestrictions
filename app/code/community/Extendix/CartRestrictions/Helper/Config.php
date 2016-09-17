<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Helper_Config
    extends Mage_Core_Helper_Abstract
{

    const EXTENDIX_CART_RESTRICTIONS_ENABLED_XML_CONFIG = 'extendix_cartrestrictions/settings/enabled';

    /**
     * @return bool
     */
    public function isActive()
    {
        if (!Mage::helper('core')->isModuleOutputEnabled('Extendix_CartRestrictions')) {
            return false;
        }

        return Mage::getStoreConfigFlag(self::EXTENDIX_CART_RESTRICTIONS_ENABLED_XML_CONFIG);
    }

}