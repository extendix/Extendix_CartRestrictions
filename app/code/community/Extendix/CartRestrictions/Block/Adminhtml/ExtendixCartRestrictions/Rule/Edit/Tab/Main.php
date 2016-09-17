<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Extendix_CartRestrictions_Block_Adminhtml_ExtendixCartRestrictions_Rule_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('extendix_cartrestrictions')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('extendix_cartrestrictions')->__('Rule Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_cart_validation_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('extendix_cartrestrictions')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('product_ids', 'hidden', array(
            'name' => 'product_ids',
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('extendix_cartrestrictions')->__('Rule Name'),
            'title' => Mage::helper('extendix_cartrestrictions')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('extendix_cartrestrictions')->__('Description'),
            'title' => Mage::helper('extendix_cartrestrictions')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('extendix_cartrestrictions')->__('Status'),
            'title'     => Mage::helper('extendix_cartrestrictions')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('extendix_cartrestrictions')->__('Active'),
                '0' => Mage::helper('extendix_cartrestrictions')->__('Inactive'),
            ),
        ));

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'     => Mage::helper('extendix_cartrestrictions')->__('Websites'),
                'title'     => Mage::helper('extendix_cartrestrictions')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $found = false;

        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array(
                'value' => 0,
                'label' => Mage::helper('extendix_cartrestrictions')->__('NOT LOGGED IN'))
            );
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('extendix_cartrestrictions')->__('Customer Groups'),
            'title'     => Mage::helper('extendix_cartrestrictions')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray(),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('extendix_cartrestrictions')->__('From Date'),
            'title'  => Mage::helper('extendix_cartrestrictions')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('extendix_cartrestrictions')->__('To Date'),
            'title'  => Mage::helper('extendix_cartrestrictions')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $form->setValues($model->getData());

        /**
         * Further development idea: May be decoupling this in UI component (Tools for developers)
         */
        $defaultMessageFieldset = $form->addFieldset('default_message_fieldset', array(
            'legend' => Mage::helper('extendix_cartrestrictions')->__('Default Message')
        ));

        $messages = $model->getStoreMessages();

        $defaultMessageFieldset->addField('store_default_message', 'text', array(
            'name'      => 'store_messages[0]',
            'required'  => true,
            'label'     => Mage::helper('salesrule')->__('Default Rule Message for All Store Views'),
            'value'     => isset($messages[0]) ? $messages[0] : '',
        ));

        $storeMessagesFieldset = $form->addFieldset('store_messages_fieldset', array(
            'legend'       => Mage::helper('extendix_cartrestrictions')->__('Store View Specific Messages'),
            'table_class'  => 'form-list stores-tree',
        ));

        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset');
        $storeMessagesFieldset->setRenderer($renderer);

        foreach (Mage::app()->getWebsites() as $website) {
            $storeMessagesFieldset->addField("w_{$website->getId()}_message", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();

                if (count($stores) == 0) {
                    continue;
                }

                $storeMessagesFieldset->addField("sg_{$group->getId()}_message", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));

                foreach ($stores as $store) {
                    $storeMessagesFieldset->addField("s_{$store->getId()}", 'text', array(
                        'name'      => 'store_messages['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => isset($messages[$store->getId()]) ? $messages[$store->getId()] : '',
                        'fieldset_html_class' => 'store',
                    ));
                }
            }
        }

        $this->setForm($form);

        Mage::dispatchEvent('extendix_cartrestrictions_rule_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

}
