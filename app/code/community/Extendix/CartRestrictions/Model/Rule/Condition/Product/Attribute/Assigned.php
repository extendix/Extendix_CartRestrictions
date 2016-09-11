<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Model_Rule_Condition_Product_Attribute_Assigned
    extends Mage_Rule_Model_Condition_Product_Abstract
{

    /**
     * The operator type which indicates whether the attribute was assigned
     */
    const OPERATOR_ATTRIBUTE_IS_ASSIGNED = 'is_assigned';

    /**
     * The operator type which indicates whether the attribute was not assigned
     */
    const OPERATOR_ATTRIBUTE_IS_NOT_ASSIGNED = 'is_not_assigned';

    /**
     * A default operator code
     */
    const DEFAULT_OPERATOR = self::OPERATOR_ATTRIBUTE_IS_ASSIGNED;

    /**
     * Operator select options hash
     * @var array
     */
    protected $_operatorSelectOptionsHash = null;

    /**
     * A cached options list
     * @var array
     */
    protected $_cachedOperatorSelectOptionsCache = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Extendix_CartRestrictions_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('extendix_cartrestrictions');
    }

    /**
     * Retrieve a product instance and initialize if needed
     * @param Varien_Object $object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct(Varien_Object $object)
    {
        return $object->getProduct() instanceof Mage_Catalog_Model_Product
            ? $object->getProduct()
            : Mage::getModel('catalog/product')->load($object->getProductId());
    }

    /**
     * Initialize options hash
     */
    public function __construct()
    {
        $this->_operatorSelectOptionsHash = array(
            self::OPERATOR_ATTRIBUTE_IS_ASSIGNED        => $this->_getHelper()->__('is assigned'),
            self::OPERATOR_ATTRIBUTE_IS_NOT_ASSIGNED    => $this->_getHelper()->__('is not assigned')
        );

        parent::__construct();
    }

    /**
     * Retrieves unary operators of the attribute assignment state
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        if (is_null($this->_cachedOperatorSelectOptionsCache)) {
            $this->_cachedOperatorSelectOptionsCache = array();
            foreach ($this->_operatorSelectOptionsHash as $operatorValue => $operatorLabel) {
                $this->_cachedOperatorSelectOptionsCache[] = array(
                    'label' => $operatorLabel,
                    'value' => $operatorValue
                );
            }
        }

        return $this->_cachedOperatorSelectOptionsCache;
    }

    /**
     * Retrieve an operator name
     * @return string
     */
    public function getOperatorName()
    {
        return $this->getOperator() && array_key_exists($this->getOperator(), $this->_operatorSelectOptionsHash)
            ? $this->_operatorSelectOptionsHash[$this->getOperator()]
            : $this->_operatorSelectOptionsHash[self::DEFAULT_OPERATOR];
    }

    /**
     * Validate a product, check whether the attribute is assigned to the product
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $product    = $this->_getProduct($object);
        $attributes = $product->getAttributes();

        return $this->getOperator() == self::OPERATOR_ATTRIBUTE_IS_ASSIGNED
            && array_key_exists($this->getAttribute(), $attributes)
            || $this->getOperator() == self::OPERATOR_ATTRIBUTE_IS_NOT_ASSIGNED
            && !array_key_exists($this->getAttribute(), $attributes);
    }

    /**
     * Generate a condition html
     * @return string
     */
    public function asHtml()
    {
        return $this->_getHelper()->__(
            'Attribute "%s" %s %s %s',
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getRemoveLinkHtml(),
            $this->getTypeElementHtml()
        );
    }

}