CodeIgniter iDeal form
======================
A CodeIgniter library to interact with the iDeal online payment method through form submission. This library should be compatible with the ING Basic and Rabobank Lite implementation.

Requirements
------------
1. PHP 5.1+
2. [CodeIgniter 2.0+](http://codeigniter.com)
3. An iDeal merchant account

Spark
-------------
This library is also released as a [Spark](http://getsparks.org). If you use this library in any other way, **don't copy the autoload.php to your config directory**.

Documentation
-------------

### Configuration
This library expects a configuration file to function correctly. A template for this file is provided with the library. These configuration settings are provided to you by your bank.

### Set up a payment

Set the payment id. You can use this for internal reference.

    $this->ideal_form->purchase_id('MYCD123456');

Add a short description to your payment. This description is shown on account statements.

    $this->ideal_form->description('Order #1234. Thanks for shopping with us.');
    
The products purchased by the customer. Can be called multiple times, for each product. Pass internal iDeal product id, description, price as a double and the quantity, which defaults to 1.

    $this->ideal_form->product('WTRBTL1', 'A pretty, blue, water bottle', 2.99, 12);
    
Finally, render the form, which produces loads of hidden field and a button to start the payment. Make sure you `echo` the returned string for the form to show up.

	$this->ideal_form->render_form();

License
-------

This project is licensed under the MIT license.

Contributing
------------
I am a firm believer of social coding, so <strike>if</strike> when you find a bug, please fork my code on [GitHub](http://github.com/joelcox/codeigniter-ideal-form) and squash it. I will be happy to merge it back in to the code base (and add you to the "Thanks to" section). If you're not too comfortable using Git or messing with the inner workings of this library, please [open a new issue](http://github.com/joelcox/codeigniter-ideal-form/issues). 