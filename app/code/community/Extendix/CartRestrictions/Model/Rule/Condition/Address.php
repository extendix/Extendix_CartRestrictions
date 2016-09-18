<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Rule_Condition_Address
    extends Mage_Rule_Model_Condition_Abstract
{

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        /**
         * @todo: Commented some of those attributes because they are not very important for the cart.
         *
         * Also 'total_qty' can be calculated during the event I am using right now (sale_quote_load_after).
         *
         * I could use sales_quote_collect_totals_after but then I can use it only in cart.
         * I have problem at Onepage Checkout because validation check for
         * error is done before sales_quote_collect_totals_after event. Basically I attach the error messages but
         * the checkout precess just don't handle them.
         */
        $attributes = array(
            'base_subtotal' => Mage::helper('extendix_cartrestrictions')->__('Subtotal'),
//            'total_qty' => Mage::helper('extendix_cartrestrictions')->__('Total Items Quantity'),
            'weight' => Mage::helper('extendix_cartrestrictions')->__('Total Weight'),
//            'payment_method' => Mage::helper('extendix_cartrestrictions')->__('Payment Method'),
//            'shipping_method' => Mage::helper('extendix_cartrestrictions')->__('Shipping Method'),
//            'postcode' => Mage::helper('extendix_cartrestrictions')->__('Shipping Postcode'),
//            'region' => Mage::helper('extendix_cartrestrictions')->__('Shipping Region'),
//            'region_id' => Mage::helper('extendix_cartrestrictions')->__('Shipping State/Province'),
//            'country_id' => Mage::helper('extendix_cartrestrictions')->__('Shipping Country'),
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'weight':
            case 'total_qty':
                return 'numeric';

//            case 'shipping_method':
//            case 'payment_method':
//            case 'country_id':
//            case 'region_id':
//                return 'select';
        }
        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id':
                return 'select';
        }
        return 'text';
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = Mage::getModel('adminhtml/system_config_source_country')
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')
                        ->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')
                        ->toOptionArray();
                    break;

                case 'payment_method':
                    $options = Mage::getModel('adminhtml/system_config_source_payment_allmethods')
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $address = $object;
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            }
            else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }

//        if ('payment_method' == $this->getAttribute() && ! $address->hasPaymentMethod()) {
//            $address->setPaymentMethod($object->getQuote()->getPayment()->getMethod());
//        }

        echo $address->getWeight();

        return parent::validate($address);
    }

}
