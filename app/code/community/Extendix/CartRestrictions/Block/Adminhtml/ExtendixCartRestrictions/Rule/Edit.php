<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Block_Adminhtml_ExtendixCartRestrictions_Rule_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'extendix_cartrestrictions';
        $this->_controller = 'adminhtml_extendixCartRestrictions_rule';

        parent::__construct();

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('extendix_cartrestrictions')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ), 10);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = Mage::registry('current_cart_validation_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('extendix_cartrestrictions')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('extendix_cartrestrictions')->__('New Rule');
        }
    }

    /**
     * Retrieve products JSON
     *
     * @return string
     */
    public function getProductsJson()
    {
        return '{}';
    }

}
