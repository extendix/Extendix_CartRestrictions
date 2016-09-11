<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Block_Adminhtml_ExtendixCartRestrictions_Quote
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'extendix_cartrestrictions';
        $this->_controller = 'adminhtml_extendixCartRestrictions_quote';
        $this->_headerText = Mage::helper('extendix_cartrestrictions')->__('Cart Restriction Rules');
        $this->_addButtonLabel = Mage::helper('extendix_cartrestrictions')->__('Add New Rule');
        parent::__construct();
    }

}
