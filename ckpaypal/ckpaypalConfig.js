/**
 * Plugin CKPaypal
 * Copyright (c) <2014> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
 
//
// DEFAULT VALUE FOR YOUR PAYPAL ACCOUNT
//
// business email - <input type="hidden" name="business" value="YOUR@PAYPALACCOUNT.COM">
paypalMail='my-email@lol.com';

// URL for the IPN file : 'http://www.mysite.com/yes/we/can/my-ipn.php'
paypalUrl='http://www.mysite.com/ipn.php';

// Return URL : 'http://www.mysite.com'
paypalHome='http://www.mysite.com';

// Currency - <input type="hidden" name="currency_code" value="USD">
paypalCurr='EUR';

// Tax rate - <input type="hidden" name="tax_rate" value="20.000">
paypalTax=0;

// 1 : Tax include in the price (that change the price in input tag. !!! Round +- 0.01 !!!) - 0 : plus tax (normal operation of paypal)
paypalTaxIncl=0;

// 1 : SANDBOX (test) - 0 : Production
paypalSand=0;

// appearance --- 'CC_LG' : large button with flag - '_LG' : large button - '_SM' : small button
paypalApp='_LG';

//  'products' or 'services' <input type="hidden" name="button_subtype" value="products">
paypalAct='products';

// Donation --- 0 : free donation - 1 : fixed value
paypalDonval=0;

// lang (en_US)
paypalLang5='en_US';