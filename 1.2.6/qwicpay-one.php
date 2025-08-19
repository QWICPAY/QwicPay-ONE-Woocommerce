<?php
/**
 * Plugin Name: QwicPay ONE
 * Plugin URI: https://qwicpay.com/
 * Description: Adds the QwicPay ONE payment method to Woocommerce
 * Version: 1.2.6
 * Author: QwicPay Pty Ltd
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: qwicpay-one
 *
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


const HOST = 'https://ice.qwicpay.com';


define('QWICPAY_ONE_MIN_PHP', '7.4');

/**
 * Add QwicPay top-level menu and submenus to WP Admin.
 * @since 1.1.16
 */
function qwicpay_one_check_requirements()
{
    // Check PHP
    if (version_compare(PHP_VERSION, QWICPAY_ONE_MIN_PHP, '<')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('QwicPay ONE requires PHP version 7.4 or higher.', 'qwicpay-one');
            echo '</p></div>';
        });
        return false;
    }

    // Check WooCommerce
    // Using class_exists for WC_Payment_Gateway is a good check for WooCommerce's presence.
    if (!class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning"><p>';
            echo esc_html__('QwicPay ONE requires WooCommerce to be activated and its checkout components available.', 'qwicpay-one');
            echo '</p></div>';
        });
        return false;
    }

    // Check permalink structure
    if (get_option('permalink_structure') === '') {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning"><p>';
            echo esc_html__('QwicPay ONE requires pretty permalinks to be enabled. Please go to Settings → Permalinks and choose any structure other than "Plain".', 'qwicpay-one');
            echo '</p></div>';
        });
        return false;
    }

    return true;
}

/**
 * Defines QwicPay Gateway Class
 * @since 1.0.0
 */
function qwicpay_init_gateway_class()
{

    class WC_Gateway_QwicPay extends WC_Payment_Gateway
    {
        protected $merchant_id;
        protected $api_key;
        protected $stage;

        public function __construct()
        {
            $this->supports = array(
                'products',
                'blocks',
            );

            $this->id = 'qwicpay';
            $this->method_title = 'QwicPay';
            $this->has_fields = false;
            $this->description = 'Credit & Debit Cards | Cards stored across any QwicPay merchant | Apple Pay | Samsung Pay ';
            $this->icon = plugin_dir_url(dirname(__FILE__)) . 'qwicpay-one/assets/qwicpay-icon-new.webp';
            $this->title = 'QwicPay';

            $this->init_form_fields();
            $this->init_settings();

            $this->merchant_id = $this->get_option('merchant_id');
            $this->api_key = $this->get_option('api_key');
            $this->stage = $this->get_option('stage');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_api_qwicpay_response', array($this, 'handle_qwicpay_response'));
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_filter('woocommerce_settings_api_form_fields_' . $this->id, array($this, 'add_status_indicator_to_settings'));
        }

        /**
         * Feature conf. to Adress bug in WC not following support array
         * @since 1.1.12
         */
        public function supports($feature)
        {
            if ($feature === 'products' || $feature === 'blocks') {
                return true;
            }
            return parent::supports($feature);
        }

        /**
         * Adds QwicPay MAP menu
         * @since 1.1.0
         */

        public function add_admin_menu()
        {
            global $menu;

            $menu_slug_exists = false;

            if (!$menu_slug_exists) {
                add_menu_page(
                    'QwicPay',
                    'QwicPay',
                    'manage_options',
                    'qwicpay-main',
                    [$this, 'render_portal_page'],
                    plugin_dir_url(__FILE__) . 'assets/qwicpay-icon.png',
                    56
                );

                remove_submenu_page('qwicpay-main', 'qwicpay-main');
            }
        }

        public function render_portal_page()
        {
            echo '<div class="wrap"><h1>Merchant Access Portal</h1>';
            echo '<iframe src="https://map.qwicpay.com" width="100%" height="800px" style="border: none;"></iframe>';
            echo '</div>';
        }

        /** 
         * Gateway options
         * @since 1.0.1
         */

        public function init_form_fields()
        {
            $this->form_fields = array(
                'merchant_id' => array(
                    'title' => 'Merchant ID',
                    'type' => 'text',
                    'description' => '',
                ),
                'api_key' => array(
                    'title' => 'API Key',
                    'type' => 'password',
                    'description' => '',
                ),
                'stage' => array(
                    'title' => 'Stage',
                    'description' => 'In Test, no payments are accepted. Use with caution',
                    'id' => 'qwicpay_stage',
                    'type' => 'select',
                    'options' => array(
                        'TEST' => 'Test',
                        'PROD' => 'Production'
                    ),
                    'default' => 'TEST',
                ),
            );
        }

        /** 
         * QwicPay state of merchants
         * @since 1.0.8
         */

        public function add_status_indicator_to_settings($form_fields)
        {
            $response = wp_remote_get(
                HOST . "/one/merchant/isup/{$this->stage}",
                [
                    'timeout' => 5,
                    'headers' => [
                        'merchant_id' => $this->merchant_id,
                        'merchant_key' => $this->api_key,
                    ]
                ]
            );
            $status = (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) ? 'Merchant is enabled for selected stage' : 'Merchant is not enabled for selected stage';

            $form_fields['qwicpay_status'] = array(
                'title' => 'Merchant Status',
                'type' => 'title',
                'description' => $status
            );

            return $form_fields;
        }

        /**
         * Handels payment call
         * @since 1.2.20
         */

        public function is_available()
        {
            // First check parent (like whether plugin is enabled)
            if (!parent::is_available()) {
                return false;
            }

            // Remote status check
            $response = wp_remote_get(
                HOST . "/one/merchant/isup/{$this->stage}",
                [
                    'timeout' => 5,
                    'headers' => [
                        'merchant_id' => $this->merchant_id,
                        'merchant_key' => $this->api_key,
                    ]
                ]
            );

            // Disable gateway if response is bad
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }

            return true;
        }

        /**
         * Handels payment call
         * @since 1.0.4
         */

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);

            $items_payload = array();

            foreach ($order->get_items() as $item) {
                $product = $item->get_product();

                // Get the product ID
                $product_id = $product ? $product->get_id() : 0;

                // Calculate the unit price (total for the line item / quantity)
                // Ensure this is in Rand.
                $item_price_in_rand = $item->get_subtotal() / $item->get_quantity();

                // Add the item details to the $items_payload array
                // Since it's a numerically indexed array, we simply append.
                $items_payload[] = array(
                    'id' => (string) $product_id, // WooCommerce Product ID
                    'title' => $item->get_name(),
                    'quantity' => $item->get_quantity(),
                    'price' => $item_price_in_rand
                );
            }

            //Build Qwicpay Once request
            $payload = array(
                'platform' => 'WOO',
                'stage' => $this->stage,
                'orderNumber' => $order_id,
                'cancelUrl' => $order->get_cancel_order_url_raw(),
                'user' => array(
                    'name' => $order->get_billing_first_name(),
                    'surname' => $order->get_billing_last_name(),
                    'email' => $order->get_billing_email()
                ),
                'billing' => array(
                    'street' => $order->get_billing_address_1(),
                    'city' => $order->get_billing_city(),
                    'postalCode' => $order->get_billing_postcode(),
                    'country' => $order->get_billing_country(),
                    'cell' => $order->get_billing_phone()
                ),
                'payment' => array(
                    'amount' => (float) ($order->get_total() * 100),
                    'currency' => $order->get_currency()
                ),
                'items' => $items_payload,
                'response' => array(
                    'url' => home_url('/wc-api/qwicpay_response')
                ),
                'metadata' => array(
                    'timestamp' => time(),
                    'order_id' => $order_id
                )
            );

            $response = wp_remote_post(HOST . '/one/merchant/payment', array(
                'method' => 'POST',
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'merchant_id' => $this->merchant_id,
                    'merchant_key' => $this->api_key,
                ),
                'body' => json_encode($payload)
            ));

            if (is_wp_error($response)) {
                wc_add_notice('Payment error: ' . $response->get_error_message(), 'error');
                return;
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (!isset($body['url'])) {
                wc_add_notice('Invalid QwicPay response.', 'error');
                return;
            }

            return array(
                'result' => 'success',
                'redirect' => $body['url']
            );
        }

        /**
         * Handels payment response
         * @since 1.0.4
         */

        public function handle_qwicpay_response()
        {
            $body = file_get_contents('php://input');

            if (empty($body)) {
                status_header(400);
                echo 'Empty request body';
                exit;
            }

            // Decode JSON
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                status_header(400);
                echo esc_html__('Invalid JSON', 'qwicpay-one');
                exit;
            }


            // Get Key from headers
            $received_key = '';
            if (isset($_SERVER['HTTP_KEY'])) {
                $received_key = sanitize_text_field(wp_unslash($_SERVER['HTTP_KEY']));
            }

            // Verify API key
            if ($received_key !== $this->api_key) {
                status_header(403);
                echo 'Invalid API Key';
                exit;
            }

            if (empty($this->api_key)) {
                status_header(403);
                echo 'Missing API KEY';
                exit;
            }


            // Extract fields
            $order_id = isset($data['metadata']['order_id']) ? absint($data['metadata']['order_id']) : null;
            $status = isset($data['payment']['transactionStatus']) ? absint($data['payment']['transactionStatus']) : null;
            $stage = isset($data['stage']) ? sanitize_text_field($data['stage']) : 'UNKNOWN';

            if (!$order_id || $status === null) {
                status_header(400);
                echo 'Missing required payment data';
                exit;
            }

            $order = wc_get_order($order_id);
            if (!$order) {
                status_header(404);
                echo 'Order not found';
                exit;
            }

            // Process based on transaction status
            switch ((int) $status) {
                case 1: // Approved
                    if (!in_array($order->get_status(), ['processing', 'completed'], true)) {
                        $order->payment_complete();
                        $order->add_order_note("QwicPay: Payment approved. Stage: $stage");
                    }
                    break;

                case 2: // Declined
                case 3: // Cancelled
                case 4: // Failed
                    $order->update_status('cancelled', 'QwicPay: Payment declined or cancelled.');
                    break;

                default:
                    $order->add_order_note("QwicPay: Received unknown payment status: $status. Stage: $stage");
                    break;
            }

            // Respond with redirect URL
            wp_send_json(array(
                'redirect' => $order->get_checkout_order_received_url(),
            ));
        }
    }

    function add_qwicpay_gateway($methods)
    {
        $methods[] = 'WC_Gateway_QwicPay';
        return $methods;
    }
    //hook the gateway
    add_filter('woocommerce_payment_gateways', 'add_qwicpay_gateway');
}


add_action('plugins_loaded', function () {
    // Perform general plugin requirements check first.
    if (!qwicpay_one_check_requirements()) {
        return;
    }

    // Initialize the WC_Gateway_QwicPay class (for classic checkout)
    qwicpay_init_gateway_class();

    // Only proceed with Blocks integration if WooCommerce Blocks classes are available.
    // This ensures AbstractPaymentMethodType exists when we try to use it.
    if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        // Include the AbstractPaymentMethodType implementation here,
        // after we've confirmed its base class exists.
        require_once plugin_dir_path(__FILE__) . 'includes/class-qwicpay-payment-method-type.php';

        // Register the block payment method type.
        // The namespace must match the one defined in class-qwicpay-payment-method-type.php
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function (\Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
                $payment_method_registry->register(new \QwicPayOne\Blocks\QwicPayPaymentMethodType());
            }
        );
    }

}, 10); // Use priority 10 or higher to ensure WooCommerce is fully loaded.


/**
 * Custom font
 * @since 1.2.10
 */
add_action('enqueue_block_assets', function () {
    wp_enqueue_style(
        'qwicpay-fonts',
        'https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&display=swap',
        [],
        '1.2.6'
    );
});

