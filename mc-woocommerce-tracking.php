<?php
/**
 * Plugin Name: MC Woocommerce Tracking
 * Plugin URI: https://github.com/mookie44/MCWootracking
 * Description: Order tracking code and courier fields 
 * Version: 2.3
 * Author: Matt Cook
 * Author URI: https://github.com/mookie44
 * Tested up to: 6.6
 * WC requires at least: 8.0
 * WC tested up to: 9.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
    if ( ! class_exists( 'MCWootracking' ) ) :
	class MCWootracking {
		
	    public function __construct(){
			add_action( 'add_meta_boxes', array($this, 'mc_add_metabox' ));
			add_action( 'woocommerce_process_shop_order_meta',  array($this, 'mc_save_tracking' ));
			add_action( 'woocommerce_view_order', array($this, 'mc_action_woocommerce_view_order'), 10, 1 ); 
			add_filter( 'woocommerce_email_order_meta_fields', array($this, 'mc_add_email_order_meta_fields'), 10, 3 );

			// HPOS
			add_action( 'before_woocommerce_init', function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );
	    }

	    public function mc_add_metabox() {
			//register meta box
			// check if HPOS enabled
			$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
				? wc_get_page_screen_id( 'shop-order' )
				: 'shop_order';
			add_meta_box( 'mc_order_packaging', __('Order Tracking','woocommerce'), array($this,'mc_order_packaging'), $screen, 'side', 'high' );
	    }

	    // add tracking input boxes to the admin order page
	    public function mc_order_packaging($order){
	    	// check if HPOS enabled
	    	if(!wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()){
	    		$order = wc_get_order( $order->ID );
	    	}
			$mc_tracking_code = $order->get_meta('_mc_tracking_code');
			$mc_courier = $order->get_meta('_mc_courier');

			echo '<input type="hidden" name="mc_order_tracking_nonce" value="' . wp_create_nonce() . '">
			<p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
			<label for="mc_tracking_code">Tracking code:</label>
			<input type="text" style="width:250px;" name="mc_tracking_code" placeholder="' . $mc_tracking_code . '" value="' . $mc_tracking_code . '">
			<label for="mc_courier">Courier:</label>
			<input type="text" style="width:250px;" name="mc_courier" placeholder="' . $mc_courier . '" value="' . $mc_courier . '">
			</p><button type="submit" class="button save_order button-primary" name="save" value="Update">Update</button>';
	    }

	    public function mc_save_tracking( $order_id ) {
			$order = wc_get_order( $order_id );
			if($order){
			    // --- Its safe for us to save the data ! --- //
			    $mc_tracking_code   = sanitize_text_field($_POST['mc_tracking_code']);
			    $mc_courier     	= sanitize_text_field($_POST['mc_courier']);
			    $order->update_meta_data( '_mc_tracking_code', $mc_tracking_code );
			    $order->update_meta_data( '_mc_courier', $mc_courier );
			    $order->save();
			}
	    }

	    // Add tracking info to myaccount order page
	    public function mc_action_woocommerce_view_order($orderid) { 
			$order = wc_get_order( $orderid );
			$mc_tracking_code = $order->get_meta( '_mc_tracking_code');
			$mc_courier = $order->get_meta( '_mc_courier');
			if(!empty(trim($mc_tracking_code))){
			    if (strpos($mc_courier, 'Royal Mail') !== false && !empty($mc_tracking_code)) {
					$link = "https://www.royalmail.com/track-your-item?trackNumber={$mc_tracking_code}";
			    }elseif (strpos($mc_courier, 'DPD') !== false && !empty($mc_tracking_code)) {
					$link = "http://www.dpd.co.uk/service/tracking?consignment={$mc_tracking_code}";
			    }elseif (strpos($mc_courier, 'UKmail') !== false && !empty($mc_tracking_code)) {
					$link = "https://www.ukmail.com/manage-my-delivery/manage-my-delivery";
			    }elseif (strpos($mc_courier, 'DHL') !== false && !empty($mc_tracking_code)) {
					$link = "http://www.dhl.co.uk/en/express/tracking.html?AWB={$mc_tracking_code}&brand=DHL";
				}elseif (strpos($mc_courier, 'Russian Post') !== false && !empty($mc_tracking_code)) {
					$link = "https://www.pochta.ru/tracking#{$mc_tracking_code}";
				}elseif (strpos($mc_courier, 'Fedex') !== false && !empty($mc_tracking_code)) {
					$link = "https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers={$mc_tracking_code}";
			    }elseif (strpos($mc_courier, 'UPS') !== false && !empty($mc_tracking_code)) {
					$link = "https://www.ups.com/track?loc=en_US&tracknum={$mc_tracking_code}";
			    }elseif (strpos($mc_courier, 'Deutschepost') !== false && !empty($mc_tracking_code)) {
			    	// check for USA and USPS
			    	$country = $order->get_shipping_country();
			    	if($country=="US"){
			    		$link = "https://tools.usps.com/go/TrackConfirmAction?tLabels={$mc_tracking_code}";
			    	}else{
			    		$link = "https://www.packet.deutschepost.com/web/portal-europe/packet_traceit?barcode={$mc_tracking_code}";
			    	}
			    }else{
					$link = "";
			    }
			    $text = '<p>Your order tracking code is: <b>'.$mc_tracking_code.'</b> <br>Sent via: <b>'.$mc_courier.'</b><br><a href="'.$link.'" class="btn" target="_blank">TRACK ORDER</a></p>';
			    _e( $text, 'mc_woo_tracking' );
			}
	    }

		public function mc_add_email_order_meta_fields( $fields, $sent_to_admin, $order_obj ) {
			if($order_obj->get_status()=="completed"){
			    $mc_tracking_code = $order_obj->get_meta( '_mc_tracking_code');
			    $mc_courier = $order_obj->get_meta( '_mc_courier');
			    if(!empty(trim($mc_tracking_code))){
			    	$text = '';
					if (strpos($mc_courier, 'Royal Mail') !== false && !empty($mc_tracking_code)) {
					    $text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='https://www.royalmail.com/track-your-item?trackNumber={$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
					}elseif (strpos($mc_courier, 'DPD') !== false && !empty($mc_tracking_code)) {
					    $text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='http://www.dpd.co.uk/service/tracking?consignment={$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
					}elseif (strpos($mc_courier, 'DHL') !== false && !empty($mc_tracking_code)) {
					    $text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='http://www.dhl.co.uk/en/express/tracking.html?AWB={$mc_tracking_code}&brand=DHL'>{$mc_tracking_code}</a></p>";
 					}elseif (strpos($mc_courier, 'Russian Post') !== false && !empty($mc_tracking_code)) {
	                    $text = "<p>Ваш заказ был отправлен Почтой России. Трек-номер: <a href='https://www.pochta.ru/tracking#{$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
 					}elseif (strpos($mc_courier, 'Fedex') !== false && !empty($mc_tracking_code)) {
	                    $text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers={$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
					}elseif (strpos($mc_courier, 'UPS') !== false && !empty($mc_tracking_code)) {
	                    $text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='https://www.ups.com/track?loc=en_US&tracknum={$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
					}elseif (strpos($mc_courier, 'Deutschepost') !== false && !empty($mc_tracking_code)) {
				    	// check for USA and USPS
				    	$country = $order_obj->get_shipping_country();
				    	if($country=="US"){
					    	$text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='https://tools.usps.com/go/TrackConfirmAction?tLabels={$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
				    	}else{
					    	$text = "<p>Your order was despatched via {$mc_courier}. Tracking Code: <a href='https://www.packet.deutschepost.com/web/portal-europe/packet_traceit?barcode={$mc_tracking_code}'>{$mc_tracking_code}</a></p>";
				    	}
					}else{
					    $text = "<br><p>Your order was despatched via {$mc_courier}. Tracking Code: {$mc_tracking_code}</p>";
					}
					$fields['tracking'] = array(
						'label' => __( 'Tracking Information', 'mc_woo_tracking' ),
						'value' => $text
					);
			    }
			}
			return $fields;
		    
		}

	}

    $MCWootracking = new MCWootracking();
    endif;
}
