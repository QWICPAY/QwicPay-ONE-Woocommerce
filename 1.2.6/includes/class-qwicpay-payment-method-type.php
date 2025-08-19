<?php
/**
 * QwicPay Payment Method Type for WooCommerce Blocks
 * @since 1.2.0
 * @package qwicpay-one
 */

namespace QwicPayOne\Blocks; 

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * QwicPayPaymentMethodType class.
 * @since 1.2.0
 */
final class QwicPayPaymentMethodType extends AbstractPaymentMethodType {
    /**
     * The name of the payment method. This must match the name registered in JavaScript.
     * @since 1.2.0
     * @var string
     */
    protected $name = 'qwicpay'; 

    /**
     * The QwicPay WC_Payment_Gateway instance.
     * @since 1.2.0
     * @var \WC_Gateway_QwicPay
     */
    private $gateway;

    /**
     * Initializes the payment method.
     * @since 1.2.0
     */
    public function initialize() {
        // Get the QwicPay gateway instance.
        // This assumes your WC_Gateway_QwicPay class is loaded and available.
        $gateways = WC()->payment_gateways()->payment_gateways();
        $this->gateway = $gateways[ $this->name ] ?? null;

        // If the gateway isn't found, we can't initialize properly.
        if ( ! $this->gateway ) {
            return;
        }

        $this->settings = $this->gateway->settings; // Access settings directly from the gateway instance.
    }

    /**
     * Returns true if the payment method is active.
     * @since 1.2.0
     * @return boolean
     */
    public function is_active() {
        if ( ! $this->gateway ) {
            return false;
        }
        return filter_var( $this->gateway->get_option( 'enabled', 'no' ), FILTER_VALIDATE_BOOLEAN );
    }

    /**
     * Returns an array of script handles to be registered for this payment method.
     * @since 1.2.0
     * @return array
     */
    public function get_payment_method_script_handles() {
        $asset_filepath = plugin_dir_path( dirname( __FILE__ ) ) . 'build/index.asset.php';
        $asset_url      = plugin_dir_url( dirname( __FILE__ ) ) . 'build/index.js';

        if ( file_exists( $asset_filepath ) ) {
            $asset = require $asset_filepath;
            $dependencies = $asset['dependencies'];
            $version      = $asset['version'];
        } else {
            $dependencies = array();
            $version      = '1.1.0'; // Fallback version.
        }

        wp_register_script(
            'qwicpay-blocks-integration',
            $asset_url,
            array_merge( $dependencies, array( 'wp-element', 'wc-blocks-registry', 'wc-settings' ) ),
            $version,
            true
        );

        return [ 'qwicpay-blocks-integration' ];
    }

    /**
     * Returns an array of script handles to be enqueued for the admin.
     * @since 1.2.0
     * @return array
     */
    public function get_payment_method_script_handles_for_admin() {
        return $this->get_payment_method_script_handles();
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment method script client side.
     *
     * @return array
     */
    public function get_payment_method_data() {
        if ( ! $this->gateway ) {
            return [];
        }

        return [
            'name'        => $this->name,
            'title'       => $this->gateway->title,
            'description' => $this->gateway->description,
            'icon'        => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/qwicpay-icon.webp', 
            'iconBase'    => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/icons/', 
            'supports'    => $this->gateway->supports,
            'stage'       => $this->gateway->get_option( 'stage' ),
        ];
    }

    /**
     * Constructor.
     * Hooks into the payment processing for the blocks checkout if needed.
     */
    public function __construct() {
        $this->name = 'qwicpay'; // Set the name property.
       
    }

}