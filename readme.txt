=== MC Woocommerce Tracking ===
Contributors: mookie4a4
Requires at least: 4.6
Tested up to: 6.6
Stable tag: 6.0
Requires PHP: 7.0
WC requires at least: 8.0
WC tested up to: 9.0

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds order tracking code and courier fields to admin screen. Also displays the tracking information on order completed email and my account->orders screen.

== Description ==

MC Woocommerce Tracking enables two new input fields on the order admin sidebar for use with tracking code and courier data. This is then shown on the my account view order page for customers to see. The order completed email will also contain the tracking and courier information.

You can update the tracking Code and Courier fields your self using the API. The two meta keys are:
'_mc_tracking_code'
'_mc_courier'

Tracking code links are supported for the following couriers:
Royal Mail
DPD
DHL
Deutsche Post
UKmail
Russian Post

The links are used whenever the tracking code is displayed.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/mc-woocommerce-tracking` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Changelog ==

= 2.3 =
* Tested with Woocommerce 9 & Wordpress 6.6, Added UPS

= 2.2 =
* Tested with Woocommerce 8.7 & Wordpress 6.5, PHP8 Tested

= 2.1 =
* Tested with Woocommerce 8.6 & Wordpress 6.4

= 2.0 =
* Updated with HPOS compatibility

= 1.6 =
* Tested with Woocommerce 8.0 & Wordpress 6.3

= 1.5 =
* Tested with Woocommerce 7.8 & Wordpress 6.2

= 1.4 =
* Tested with Woocommerce 7.3 & Wordpress 6.1

= 1.3 =
* Tested with Woocommerce 6.8 & Wordpress 6.0

= 1.2 =
* Tested with Woocommerce 6.0

= 1.1 =
* Updated method for email tracking links

= 1.0.20 =
* Tested with Wordpress 5.8

= 1.0.19 =
* Tested with Woocommerce 5.3

= 1.0.18 =
* Tested with Woocommerce 5.1 and Wordpress 5.7

= 1.0.17 =
* Tested with Woocommerce 5.0

= 1.0.16 =
* Tested with Woocommerce 4.8 and Wordpress 5.6

= 1.0.15 =
* Tested with Woocommerce 4.6 and added Fedex tracking link

= 1.0.14 =
* Tested with Woocommerce 4.5

= 1.0.13 =
* Tested with Woocommerce 4.0

= 1.0.12 =
* Tested with Woocommerce 3.9, Added Russian Post

= 1.0.11 =
* Tested with Wordpress 5.3

= 1.0.10 =
* Compatibility with Woocommerce 3.8

= 1.0.9 =
* Update Deutschepost tracking link to check for USA shipping and use USPS

= 1.0.8 =
* Added "Deutschepost" tracking link

= 1.0.7 =
* Tested for Woocommerce 3.7

= 1.0.6 =
* Tested for Wordpress 5.2

= 1.0.5 =
* Changed save tracking priority (Fixed completed order email bug)
* Tested for Woocommerce 3.6

= 1.0.4 =
* Tested for Wordpress 5.1

= 1.0.3 =
* Added update button on order view page. Thanks to Rajikaru!

= 1.0.2 =
* Tested for Wordpress 5.0

= 1.0.1 =
* Tested for Woocommerce 3.5.

= 1.0 =
* It's alive!
* Display tracking info on order completed email.
