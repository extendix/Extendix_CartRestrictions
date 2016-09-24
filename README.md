# Extendix_CartRestrictions
Magento 1.x extension that allows Magento administrators to create rules that if valid stop the checkout process in cart. Basically customer wouldn't be able to go to checkout page until modify his/her cart.

# Capabilities

This Magento extension allows you to use similar to Cart Price Rules admin interface in order to create rules that can restrict Magento cart.

What I mean with "... restrict Magento cart."? I mean that if rule conditions are covered/valid then the customer would see error message in cart and would not be abled to continue to checkout cart until the cart is modified in order to pass the validation.

Example cart page where a customer was stopped to continue the checkout process:

![Magento Cart Validation](http://ceckoslab.com/wp-content/uploads/2016/09/Shopping-Cart-Validation.png)

Rule conditions:
 * Customer Group: Wholesale
 * Subtotal: less than 500
 
Rule message: "Minimun basket subtotal for wholesale customers is 500 Euro! Please contact us in case you want to decrease this limit."

# Screen shot from example admin config:

Rule General Page:
![Rule General Page](http://ceckoslab.com/wp-content/uploads/2016/09/Rule-General-Page.png)


Rule Conditions Page:
![Rule Conditions Page](http://ceckoslab.com/wp-content/uploads/2016/09/Rule-Conditions-Page.png)

# What's more?

Actually the extension allows you to do much more than what is shown in the screenshots above. So what else?
 * Specify date range in which a rule will be valid
 * Specify customer groups for which the rule will be valid
 * Specify website for which the rule will be valid
 * Specify different user notification messages per store view
 * Specify different condition combinations between cart, product attributes, categories and etc.
 
Once you install the extension you can create rules in Magento admin from: Sales -> Cart Restrictions -> Manage Restrictions -> Add Rule

# Use this extension with conscious because if you create wrong rule then this may "kill" your conversion rate!
