<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Rule_Condition_Combine
    extends Mage_Rule_Model_Condition_Combine
{

    public function __construct()
    {
        parent::__construct();
        $this->setType('extendix_cartrestrictions/rule_condition_combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('extendix_cartrestrictions/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'extendix_cartrestrictions/rule_condition_address|'.$code, 'label'=>$label);
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'extendix_cartrestrictions/rule_condition_product_found', 'label'=>Mage::helper('extendix_cartrestrictions')->__('Product attribute combination')),
            array('value'=>'extendix_cartrestrictions/rule_condition_product_subselect', 'label'=>Mage::helper('extendix_cartrestrictions')->__('Products subselection')),
            array('value'=>'extendix_cartrestrictions/rule_condition_combine', 'label'=>Mage::helper('extendix_cartrestrictions')->__('Conditions combination')),
            array('label'=>Mage::helper('extendix_cartrestrictions')->__('Cart Attribute'), 'value'=>$attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('extendix_cartrestrictions_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
