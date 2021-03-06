<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Rule_Condition_Product_Found
    extends Extendix_CartRestrictions_Model_Rule_Condition_Product_Combine
{

    public function __construct()
    {
        parent::__construct();
        $this->setType('extendix_cartrestrictions/rule_condition_product_found');
    }

    /**
     * Load value options
     *
     * @return Extendix_CartRestrictions_Model_Rule_Condition_Product_Found
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1 => Mage::helper('extendix_cartrestrictions')->__('FOUND'),
            0 => Mage::helper('extendix_cartrestrictions')->__('NOT FOUND')
        ));
        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('extendix_cartrestrictions')->__("If an item is %s in the cart with %s of these conditions true:", $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        $all = $this->getAggregator()==='all';
        $true = (bool)$this->getValue();
        $found = false;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if (($found && $true) || (!$true && $found)) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        }
        // not found and we're making sure it doesn't exist
        elseif (!$found && !$true) {
            return true;
        }
        return false;
    }

}
