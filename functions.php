<?php
/**
 * Plugin Name: Express Shipping Method for WooCommerce
 * Plugin URI: https://r2adevelopment.am
 * Description: Express Shipping Method for WooCommerce
 * Version: 1.0.0
 * Author: Yervand Soghomonyan
 * Domain Path: /lang
 */

if ( ! defined( 'WPINC' ) ) {

    die;

}

/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action('woocommerce_shipping_init', 'express_method');
    function express_method()
    {

        if (!class_exists('Express_Shipping_Method')) {
            class Express_Shipping_Method extends WC_Shipping_Method
            {

                public function __construct($instance_id = 0)
                {
                    $this->id = 'express';
                    $this->instance_id = absint($instance_id);
                    $this->domain = 'rasq';
                    $this->method_title = __('Express Shipping', $this->domain);
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings',
                        'instance-settings-modal',
                    );
                    $this->init();
                }

## Load the settings API
                function init()
                {
                    $this->init_form_fields();
                    $this->init_settings();
                    $this->enabled = $this->get_option('enabled', $this->domain);
                    $this->title = $this->get_option('title', $this->domain);
                    $this->method_description = $this->get_option('description', $this->domain);
                    $this->info = $this->get_option('info', $this->domain);
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                function init_form_fields()
                {
                    $this->instance_form_fields = array(
                        'title' => array(
                            'type' => 'text',
                            'title' => __('Title', $this->domain),
                            'description' => __('Title to be displayed on site.', $this->domain),
                            'default' => __('Express ', $this->domain),
                        ),
                        'description' => array(
                            'type' => 'textarea',
                            'title' => __('Description', $this->domain),
                            'description' => __('Description of method.', $this->domain),
                            'default' => __('Get the product earlier at your doorstep. ', $this->domain),
                        ),
                        'cost' => array(
                            'type' => 'number',
                            'title' => __('Cost', $this->domain),
                            'description' => __('Enter a cost', $this->domain),
                            'default' => '0',
                        ),
                    );
                }

                public function calculate_shipping($packages = array())
                {
                    $cost = $this->get_option('cost', $this->domain);
                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost,
                    );
                    $this->add_rate($rate);
                }
            }
        }
    }

    add_filter('woocommerce_shipping_methods', 'add_express');
    function add_express($methods)
    {
        $methods['express'] = 'Express_Shipping_Method';
        return $methods;
    }
}
