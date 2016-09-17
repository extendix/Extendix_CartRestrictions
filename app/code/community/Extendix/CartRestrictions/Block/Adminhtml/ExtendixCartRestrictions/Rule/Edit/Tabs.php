<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Block_Adminhtml_ExtendixCartRestrictions_Rule_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('restriction_rule_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('extendix_cartrestrictions')->__('Cart Restriction Rules'));
    }

}
