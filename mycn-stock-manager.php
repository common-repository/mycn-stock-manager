<?php
/**
 * @wordpress-plugin
 * Plugin Name:       MyCN Stock Manager
 * Plugin URI:        https://mycn.io/wp-mycn/
 * Description:       Easily manage your MyCN stock.
 * Version:           1.0.0
 * Author:            MyCN
 * Author URI:        https://mycn.io/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       mycn-stock-manager
 * Requires at least: 5.0
 * Tested up to:  5.4.2
 * WC requires at least: 4.0.0
 * WC tested up to: 4.2.2
 */

/**
 * Register the custom product type after init
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( ! class_exists( 'WC_Settings_MSM' ) ) :
function msm_plugin_setting() {

	/**
	 * Settings class
	 *
	 * @since 1.0.0
	 */
	class WC_Settings_MSM extends WC_Settings_Page {


		/**
		 * Setup settings class
		 *
		 * @since  1.0
		 */
		public function __construct() {

			$this->id    = 'mycnstock';
			$this->label = __( 'MyCN Stock', 'my-textdomain' );

			add_filter( 'woocommerce_settings_tabs_array',        array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id,      array( $this, 'msm_output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'msm_save' ) );
			add_action( 'woocommerce_sections_' . $this->id,      array( $this, 'output_sections' ) );

		}


		/**
		 * Get sections
		 *
		 * @return array
		 */
        public function msm_get_sections() {

			$sections = array(
				''         => __( 'Section 1', 'my-textdomain' ),
			);

			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}

		/**
		 * Get settings array
		 *
		 * @since 1.0.0
		 * @param string $current_section Optional. Defaults to empty string.
		 * @return array Array of settings
		 */
		public function msm_get_settings( $current_section = '' ) {



				/**
				 * Filter Plugin Section 2 Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 *
				 */
				 if ( 'second' == $current_section ) {
				 }else{
				     	$settings = apply_filters( 'msm_section2_settings', array(

					array(
						'name' => __( 'Group 1', 'my-textdomain' ),
						'type' => 'title',
						'desc' => '',
						'id'   => 'msm_group1_options',
					),


					 array(
            			'title'       => 'Enable/Disable',
            			'label'       => 'Enable MyCN Stock',
            			'type'        => 'checkbox',
            			//'desc'      => apply_filters( 'msm_enabled_option_mycn', sprintf(__( 'Please click <a target="_blank" href="%s">here</a> to activate your account' ),
					//	'https://wpwham.com/products/sku-generator-for-woocommerce/'
				//	), 'msm_section2_settings'
			//	),
            			'default'     => 'no',
            				'desc_tip'    => true,
            			'id'=>'msm_enabled_option_mycn'
            		),
                    array(
            			'title'       => 'Store Name',
            			'type'        => 'text',
            			'id'=>'msm_myplugin_section2_settings'
            		),
            		array(
            			'title'       => 'MyCN username',
            			'type'        => 'text',
            			'id'=>'msm_username_option_mycn'
            		),

            		array(
            			'title'       => 'MyCN password',
            			'type'        => 'password',
            			'id'=>'msm_password_option_mycn',
            		),
            		array(
        				'title'     => 'China wearhouse',
        				'id'        => 'msm_china_option_mycn',
        				'default'   => 'yes',
        				'type'      => 'checkbox',
        			),
        			array(
        				'title'     => 'Saudi Arabia wearhouse',
        				'id'        => 'msm_saudi_option_mycn',
        				'default'   => 'yes',
        				'type'      => 'checkbox',
        			),
            		 array(
            			'title'       => 'Key token',
            			'type'        => 'text',
            			'desc_tip'    => true,
            			'custom_attributes' => apply_filters( 'msm_token_option_mycn', array(  'disabled' => 'disabled', 'required' => 'required' ), 'msm_section2_settings' ),
            			'id'=>'msm_token_option_mycn'
            		),
            		array(
            			'title'       => '',
            			'type'        => 'text',
            			'desc_tip'    => true,
            			'custom_attributes' => apply_filters( 'msm_token_option_mycn', array('hidden' => 'hidden'), 'msm_section2_settings' ),
            			'id'=>'msm_hiddenstatus_option_mycn'
            		),


					array(
						'type' => 'sectionend',
						'id'   => 'msm_group1_options'
					),


				) );

				 }

			/**
			 * Filter MyPlugin Settings
			 *
			 * @since 1.0.0
			 * @param array $settings Array of the plugin settings
			 */
			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

		}


		/**
		 * Output the settings
		 *
		 * @since 1.0
		 */
		public function msm_output() {

			global $current_section;

			$settings = $this->msm_get_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}


		/**
	 	 * Save settings
	 	 *
	 	 * @since 1.0
		 */
		public function msm_save() {

			global $current_section;

			$settings = $this->msm_get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );

			$username= sanitize_text_field($_POST['msm_username_option_mycn']);
			$passrord= sanitize_text_field($_POST['msm_password_option_mycn']);

			$option='msm_token_option_mycn';
			$value=msm_getToken($username,$passrord);
			update_option( $option, $value);

			if(msm_getIsActiveMerchant()){
			    update_option( 'msm_hiddenstatus_option_mycn', 'active');
			}else{
			    update_option( 'msm_hiddenstatus_option_mycn', 'not_active');
			}



		}

	}

	return new WC_Settings_MSM();

}
add_filter( 'woocommerce_get_settings_pages', 'msm_plugin_setting', 15 );

endif;
function msm_register_mycn_product_product_type() {

	/**
	 * This should be in its own separate file.
	 */
	class Msm_MyCN_Product extends WC_Product {

		public function __construct( $product ) {

			$this->product_type = 'mycn_product';

			parent::__construct( $product );

		}



	}

}
add_action( 'plugins_loaded', 'msm_register_mycn_product_product_type' );


/**
 * Add to product type drop down.
 */
function add_mycn_product_product( $types ){

	// Key should be exactly the same as in the class
	$types[ 'mycn_product' ] = __( 'Mycn Product' );

	return $types;

}
if(get_option('msm_enabled_option_mycn')=='yes'){
    add_filter( 'product_type_selector', 'add_mycn_product_product' );
}



/**
 * Show pricing fields for mycn_product product.
 */
function mycn_product_custom_js() {

	if ( 'product' != get_post_type() ) :
		return;
	endif;

	?><script type='text/javascript'>
		jQuery( document ).ready( function() {
			jQuery( '.options_group.pricing' ).addClass( 'show_if_mycn_product' ).show();
		});

	</script><?php

}
add_action( 'admin_footer', 'mycn_product_custom_js' );


/**
 * Add a custom product tab.
 */
function msm_custom_product_tabs( $tabs) {

	$tabs['mycn'] = array(
		'label'		=> __( 'MyCN Stock', 'woocommerce' ),
		'target'	=> 'mycn_options',
		'class'		=> array( 'show_if_mycn_product', 'show_if_variable_mycn'  ),
	);

	return $tabs;

}
if(get_option('msm_enabled_option_mycn')=='yes'){
    add_filter( 'woocommerce_product_data_tabs', 'msm_custom_product_tabs' );
}



/**
 * Contents of the mycn options product tab.
 */
function msm_mycn_options_product_tab_content() {

	global $post;

	?><div id='mycn_options' class='panel woocommerce_options_panel'><?php

		?><div class='options_group'><?php

            $all_products=fetch_products();

            echo '<p class="form-field _text_input_y_field ">
            <label for="_text_input_y">Select Product From MyCN</label>
		    <select name="_sku" id="_text_input_y" class="select short">';
            foreach ($all_products as $all_product){

               echo $all_product['id'];
               $the_sku=$all_product['shipment_id'];
               $the_title=$all_product['title'];
               if($all_product['title']=="" || $all_product['title']== NULL){
                   $the_title=$all_product['id'];
               }else{
                   $the_title=$all_product['title'];
               }
               echo '<option value="' . $the_sku . '">' . $the_title . '</option>';


            }
            echo '</select></p>';




		?></div>

	</div><?php


}
if(get_option('msm_enabled_option_mycn')=='yes'){

    add_action( 'woocommerce_product_data_panels', 'msm_mycn_options_product_tab_content' );

}


/**
 * Save the custom fields.
 */
function msm_save_option_field( $post_id ) {

// 	$mycn_option = isset( $_POST['_enable_mycn_option'] ) ? 'yes' : 'no';
// 	update_post_meta( $post_id, '_enable_mycn_option', $mycn_option );

	if ( isset( $_POST['_sku'] )) {
	    $msm_sku=sanitize_text_field($_POST['_sku']);
		update_post_meta( $post_id, '_sku', $msm_sku );
    }

}

add_action( 'woocommerce_process_product_meta_mycn_product', 'msm_save_option_field');
add_action( 'woocommerce_process_product_meta_variable_mycn', 'msm_save_option_field');





add_action( "woocommerce_mycn_product_add_to_cart", function() {
    do_action( 'woocommerce_simple_add_to_cart' );
});
/**
 * Hide Attributes data panel.
 */
// function hide_attributes_data_panel( $tabs) {

// 	$tabs['attribute']['class'][] = 'hide_if_mycn_product hide_if_variable_mycn';

// 	return $tabs;

// }
// add_filter( 'woocommerce_product_data_tabs', 'hide_attributes_data_panel' );

function msm_getToken($username,$password) {
  $body = array(
      'username'    => $username,
      'password'   => $password,
      'remember_me' => '1',
  );
  $args = array(
    'body'        => $body,
    'timeout'     => '0',
    'redirection' => '5',
    'httpversion' => '1.0',
    'blocking'    => true,
    'headers'     => array(),
    'cookies'     => array(),
  );
  $response = wp_remote_post( 'https://api.mycn.io/api/auth/login', $args );

  if ( is_array( $response ) && ! is_wp_error( $response ) ) {
  $headers = $response['headers']; // array of http header lines
  $body    = $response['body']; // use the content
  }
  $array_result = json_decode($body, true);
  if($array_result['status']==true){
   $token = $array_result['access_token'];
  }else{
   $token = '';

  }


  return $token;
  }
  function msm_getIsActiveMerchant() {
    $username=get_option('msm_username_option_mycn');
    $password=get_option('msm_password_option_mycn');
    $token=msm_getToken($username,$password);
      $store_name=get_option('msm_myplugin_section2_settings');
      if(get_option('msm_enabled_option_mycn')=='yes'){
          $store_status=1;
      }else{
          $store_status=0;
      }

      $body = array(
          'name'    => $store_name,
          'status'   => $store_status,
      );
      $args = array(
        'body'        => $body,
        'timeout'     => '0',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(
          'Accept' => 'application/json',
          'Content-Type' => 'application/x-www-form-urlencoded',
          'Authorization' => 'Bearer ' . $token 
        ),
        'cookies'     => array(),
      );
      $response = wp_remote_post( 'https://api.mycn.io//api/status', $args );
    
      if ( is_array( $response ) && ! is_wp_error( $response ) ) {
      $headers = $response['headers']; // array of http header lines
      $body    = $response['body']; // use the content
      }
      $array_result = json_decode($body, true);
      return $array_result['status'];
    
  }
  function fetch_products() {
      $username=get_option('msm_username_option_mycn');
      $password=get_option('msm_password_option_mycn');
      $token=msm_getToken($username,$password);




    $store_name=get_option('msm_myplugin_section2_settings');
    $all_products=array();
    $all_products_cn=array();
    $all_products_sa=array();
            $args = array(
            'headers' => array(
                'Accept' => 'application/json',
                'php-auth-user' => 'user',
                'php-auth-pw' => 'pass',
                'Authorization' => 'Bearer ' . $token
            )
        );
    if(get_option('msm_china_option_mycn')=='yes'){

        $response_cn=wp_remote_get( 'https://api.mycn.io//api/shipments/warehouse/fetch_products', $args );
        
        if ( is_array( $response_cn ) && ! is_wp_error( $response_cn ) ) {
      $headers = $response_cn['headers']; // array of http header lines
      $body_cn    = $response_cn['body']; // use the content
      }
      $array_result_cn = json_decode($body_cn, true);
       
        if($array_result_cn['status']==true){
            foreach ($array_result_cn['data'] as $product_china){
                 $all_products_cn[]=array(
                'id'=>$product_china['id'],
                'shipment_id'=>'sa-'.$product_china['barcode'],
                'title' =>$product_china['title'],
                );
            }
        }
    }
    if(get_option('msm_saudi_option_mycn')=='yes'){
        
        $response_sa=wp_remote_get( 'https://api.mycn.io//api/saudi/shipments/warehouse/fetch_products', $args );
        
        if ( is_array( $response_sa ) && ! is_wp_error( $response_sa ) ) {
      $headers = $response_sa['headers']; // array of http header lines
      $body_sa    = $response_sa['body']; // use the content
      }
      $array_result_sa = json_decode($body_sa, true);
       
        if($array_result_sa['status']==true){
            foreach ($array_result_sa['data'] as $product_sa){
                 $all_products_sa[]=array(
                'id'=>$product_sa['id'],
                'shipment_id'=>'sa-'.$product_sa['barcode'],
                'title' =>$product_sa['title'],
                );
            }
        }
    }

    $all_products=array_merge($all_products_cn,$all_products_sa);
     return $all_products;



  }



add_action( 'woocommerce_order_status_changed', 'send_shipment_request_to_mycn', 10, 3 );
function send_shipment_request_to_mycn( $order_id, $old_status, $new_status ) {
    global $woocommerce, $post;


    if ( $new_status == "completed" ) {

        $order = wc_get_order( $order_id );
        $items = $order->get_items();

        $order_data = $order->get_data(); // The Order data

        $address = array(
            'first_name'=>$order_data['shipping']['first_name'],
            'last_name'=>$order_data['shipping']['last_name'],
            'address_1'=>$order_data['shipping']['address_1'],
            'address_2'=>$order_data['shipping']['address_2'],
            'city'=>$order_data['shipping']['city'],
            'state'=>$order_data['shipping']['state'],
            'postcode'=>$order_data['shipping']['postcode'],
            'country'=>strtolower($order_data['shipping']['country']),
            'phone'=>$order_data['billing']['phone']

            );
        $phone=$order->billing_phone;
        $address_id = msm_createAddress($address,$phone);
        $china_products=array();
        $saudi_products=array();
        foreach ( $items as $item ) {
            $product_name = $item->get_name();
            $product_id = $item->get_product_id();
            $quantity = $item->get_quantity();

            $product_variation_id = $item->get_variation_id();
           // wc_update_order_item_meta(15, '_qty', $product_id);
            $product = wc_get_product( $product_id );
             if( $product->is_type( 'mycn_product' ) ) {
                $sku = get_post_meta( $product_id, '_sku', true );
                $sku_wearhouse = substr($sku, 0, 2);

                if($sku_wearhouse=='cn'){

                    preg_match('/cn-(.*?)/', $sku, $barcode_cn);
                    $specifications[] = array(
				'attribute_text'            => 'لا يوجد مواصفات',
				'attribute_value_text'        => 'لا يوجد مواصفات'

			    );
                   // $china_products[]=$barcode_cn[1];
                    $shipments_cn[] = array(
					'name'            => $product_name,
					'quantity'        => $quantity,
					'sku'             => $barcode_cn[1],
					'specifications'  => $specifications
				    );
                    $post_cn= array(
        			'address_id' => $address_id,
        			'requested_by'  => get_option('msm_myplugin_section2_settings'),
        			'shipments'  => $shipments_cn
        				);
                    $the_array_cn = json_encode($post_cn);
                    msm_sendShipmentRequestCn($the_array_cn);

                }elseif($sku_wearhouse=='sa'){
                     preg_match('/sa-(.*?)/', $sku, $barcode_sa);


                    $specifications_sa[] = array(
				'attribute_text'            => 'لا يوجد مواصفات',
				'attribute_value_text'        => 'لا يوجد مواصفات'

			    );

                    $shipments_sa[] = array(
					'name'            => $product_name,
					'quantity'        => $quantity,
					'sku'             => $barcode_sa[1],
					'specifications'  => $specifications_sa
				    );
                    $post_sa= array(
        			'address_id' => $address_id,
        			'requested_by'  => get_option('msm_myplugin_section2_settings'),
        			'shipments'  => $shipments_sa
        				);
                    $the_array_sa = json_encode($post_sa);
                    msm_sendShipmentRequestSa($the_array_sa);

                   // $saudi_products[]=$barcode_sa[1];

                }

            }

            $pruducts_to_mycn[]=array(
                'sku'=>$product->get_sku()
            );
            $room = get_post_meta( $product_id, 'room', true );

        }

    }

}

function msm_sendShipmentRequestCn($body) {

     $username=get_option('msm_username_option_mycn');
      $password=get_option('msm_password_option_mycn');
      $token=msm_getToken($username,$password);
      $args = array(
        'body'        => $body,
        'timeout'     => '0',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(
          'Accept' => 'application/json',
          'Content-Type' => 'application/x-www-form-urlencoded',
          'Authorization' => 'Bearer ' . $token 
        ),
        'cookies'     => array(),
      );
      $response = wp_remote_post( 'https://api.mycn.io//api/shipments/request/create', $args );
      if ( is_array( $response ) && ! is_wp_error( $response ) ) {
      $headers = $response['headers']; // array of http header lines
      $body    = $response['body']; // use the content
      }
      $array_result = json_decode($body, true);
      if($array_result){
        if($array_result['status']==true){
          $shipment_id = $array_result['data'];
          return $shipment_id['Shipment_id'];
        }
      }
  }

function msm_sendShipmentRequestSa($body) {
  $username=get_option('msm_username_option_mycn');
   $password=get_option('msm_password_option_mycn');
   $token=msm_getToken($username,$password);
   $args = array(
     'body'        => $body,
     'timeout'     => '0',
     'redirection' => '5',
     'httpversion' => '1.0',
     'blocking'    => true,
     'headers'     => array(
       'Accept' => 'application/json',
       'Content-Type' => 'application/x-www-form-urlencoded',
       'Authorization' => 'Bearer ' . $token 
     ),
     'cookies'     => array(),
   );
   $response = wp_remote_post( 'https://api.mycn.io//api/saudi/shipments/request/create', $args );
   if ( is_array( $response ) && ! is_wp_error( $response ) ) {
   $headers = $response['headers']; // array of http header lines
   $body    = $response['body']; // use the content
   }
   $array_result = json_decode($body, true);
   if($array_result){
     if($array_result['status']==true){
       $shipment_id = $array_result['data'];
       return $shipment_id['Shipment_id'];
     }
   }

  }


function msm_createAddress($address,$phone){

    $username=get_option('msm_username_option_mycn');
      $password=get_option('msm_password_option_mycn');
      $token=msm_getToken($username,$password);
      $body = array('first_name' => $address['first_name'],'second_name' => '','third_name' => '','last_name' => $address['last_name'],
    'country_code' => $address['country'],'city' => $address['city'],
    'region' => $address['state'],'neighborhood' => $address['address_2'],'street' => $address['address_1'],
    'zip_code' => $address['postcode'],'phone' => $phone);
    $args = array(
     'body'        => $body,
     'timeout'     => '0',
     'redirection' => '5',
     'httpversion' => '1.0',
     'blocking'    => true,
     'headers'     => array(
       'Accept' => 'application/json',
       'Content-Type' => 'application/x-www-form-urlencoded',
       'Authorization' => 'Bearer ' . $token 
     ),
     'cookies'     => array(),
   );
   $response = wp_remote_post( 'https://api.mycn.io//api/address/create', $args );
   if ( is_array( $response ) && ! is_wp_error( $response ) ) {
   $headers = $response['headers']; // array of http header lines
   $body    = $response['body']; // use the content
   }
   $array_result = json_decode($body, true);
   if($array_result){
     if($array_result['status']==true){
       $address_id = $array_result['data'];
       return $address_id['id'];
     }
   }
 

}
