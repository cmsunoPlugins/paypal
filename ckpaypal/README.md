CKPaypal
========

CKPaypal is a Paypal plugin for CKEditor.
It adds a Paypal button in the CKEditor taskbar.

Installation
------------

Add the following code in config.js

```
config.extraPlugins='ckpaypal';
config.extraAllowedContent='input[*]';
```

Specifications
--------------

###Available Paypal buttons

 * Buy,
 * Add to Cart,
 * View Cart,
 * Donate,
 * Subscribe.

###Config

A configuration file allow to define permanent elements (account, URL ...) and default choices (button appearance...).
You can also switch to Sandbox mode.

###Features

You can add as many button as you want in your page. This plugin allow you to create a small shop very easily.

When you double-click a created button, the window opens with the updated elements which facilitates changes.

IPN file included. It must be completed, adapted and possibly moved.

Tested on CKEditor 4.3.2

Support
-------

Demo, screenshots and more details in french [here](http://www.boiteasite.fr/fiches/ckpaypal.html)

You can test the power of this plugin with [CMSUno](https://github.com/boiteasite/cmsuno)

Thank you to report bugs.

License
-------

CKPaypal is under MIT license.

<pre>
Copyright (c) <2014> <Jacques Malgrange contacter@boiteasite.fr>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
</pre>

Versions
--------
CKPaypal Version 1.0 - 22/11/2014
