<?php

/*
Plugin Name: Accept CryptoCurrency Payments on WooCommerce - TheBigCoin
Plugin URI:
Description: Accept CryptoCurrency payments on your WooCommerce store with TheBigCoin payments plugin.
Version: 1.0.2
Author: TheBigCoin
Author URI: https://www.thebigcoin.io
License: MIT License
License URI: https: http://opensource.org/licenses/MIT
Github Plugin URI: 
*/

if (!defined('ABSPATH')) {
    die('Access denied.');
}

add_action('plugins_loaded', 'thebigcoin_init');

define('TBC_WOOCOMMERCE_PAYMENT_VERSION', '1.0.1');

function thebigcoin_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    };

    require_once(__DIR__ . '/includes/thebigcoinclient/init.php');

    class WC_Gateway_Thebigcoin extends WC_Payment_Gateway
    {

        private $tbcClient;

        public function __construct()
        {
            $this->id = 'thebigcoin';
            $this->has_fields = false;
            $this->order_button_text = __('Pay with TheBigCoin', 'woocommerce');
            $this->method_title = __('TheBigCoin', 'woocommerce');

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->project_id = $this->get_option('project_id');
            $this->api_key = $this->get_option('api_key');
            $this->api_secret = $this->get_option('api_secret');
            $this->test_mode = ('yes' === $this->get_option('api_test_mode', 'no'));
            $this->order_statuses = $this->get_option('order_statuses');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'save_statuses'));
            add_action('woocommerce_api_wc_gateway_thebigcoin', array($this, 'callback'));
        }

        public function admin_options()
        {
            $thebigcoin_api_status = get_option("thebigcoin_api_status");
            $thebigcoin_api_show = get_option("thebigcoin_api_status_show");

            echo '<h3>' . __('TheBigCoin', 'woothemes') . '</h3>';
            echo '<p>' . __('TheBigCoin helps you accept CryptoCurrency payments on your WooCommerce online store. For any inquiries or questions regarding TheBigCoin plugin please email us at <a href="mailto:support@thebigcoin.io">support@thebigcoin.io</a>', 'thebigcoin') . '</p>';
            if ($thebigcoin_api_status && $thebigcoin_api_show) {
                echo '<p><b>' . __('Successfully connected TheBigCoin API.', 'thebigcoin') . '</b></p>';
            } elseif ($thebigcoin_api_show) {
                echo '<p><b>' . __('Could not connect TheBigCoin API.', 'thebigcoin') . '</b></p>';
            }
            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
        }

        public function process_admin_options()
        {
            if (!parent::process_admin_options())
                return false;

            $project_id = $this->get_option('project_id');
            $api_key = $this->get_option('api_key');
            $api_secret = $this->get_option('api_secret');
            $env = ('yes' === $this->get_option('api_test_mode', 'no'));

            if ($project_id && $api_key && $api_secret) {
                $tbcClient = new \TheBigCoinClient\TheBigCoinClient(
                    array(
                        'project_id' => $project_id,
                        'api_key' => $api_key,
                        'api_secret' => $api_secret,
                        'env' => ($env ? 'test' : 'live'),
                        'user_agent' => 'TheBigCoin/Payment Plugin: ' . TBC_WOOCOMMERCE_PAYMENT_VERSION . '/' . 'WooCommerce: ' . WOOCOMMERCE_VERSION
                    )
                );

                try {
                    $test_api = $tbcClient->testApi();
                    if (isset($test_api['test']) && $test_api['test']['status']) {
                        update_option("thebigcoin_api_status", true);
                        update_option("thebigcoin_api_status_show", true);
                    } else {
                        update_option("thebigcoin_api_status", false);
                        update_option("thebigcoin_api_status_show", true);
                    }
                } catch (\Exception $e) {
                    update_option("thebigcoin_api_status", false);
                    update_option("thebigcoin_api_status_show", true);
                    return false;
                }
            } else {
                update_option("thebigcoin_api_status", false);
            }
        }

        public function get_icon()
        {
            $payment_icon = plugins_url('assets/images/thebigcoin-button.png', __FILE__);
            $payment_icon_html = '<img src="' . esc_attr($payment_icon) . '" alt="' . esc_attr__('TheBigCoin', 'woocommerce') . '" />';
            return apply_filters('woocommerce_gateway_icon', $payment_icon_html, $this->id);
        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable TheBigCoin', 'woocommerce'),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                    'default' => __('CryptoCurrency', 'woocommerce'),
                    'desc_tip' => true
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'woocommerce'),
                    'default' => __('Pay with CryptoCurrency via TheBigCoin', 'woocommerce'),
                    'desc_tip' => true
                ),
                'api_details' => array(
                    'title' => __('API credentials', 'woocommerce'),
                    'type' => 'title',
                    'description' => sprintf(__('Enter your TheBigCoin API credentials.', 'thebigcoin')),
                ),
                'project_id' => array(
                    'title' => __('Project ID', 'woocommerce'),
                    'description' => __('Please enter your TheBigCoin Project ID.', 'thebigcoin'),
                    'type' => 'text',
                    'desc_tip' => true
                ),
                'api_key' => array(
                    'title' => __('API Key', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Please enter your TheBigCoin API Key.', 'thebigcoin'),
                    'default' => __('', 'woocommerce'),
                    'desc_tip' => true
                ),
                'api_secret' => array(
                    'title' => __('API Secret', 'woocommerce'),
                    'type' => 'text',
                    'default' => '',
                    'description' => __('Please enter your TheBigCoin API Secret.', 'thebigcoin'),
                    'desc_tip' => true
                ),
                'api_test_mode' => array(
                    'title' => __('Test Mode', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable Test Mode', 'woocommerce'),
                    'default' => 'no',
                    'description' => __('You can use TheBigCoin test API. Find out more about the <a href="https://app.thebigcoin.io/docs/api/api-testing" target="_blank">TheBigCoin test API</a>.', 'thebigcoin'),
                ),
                'order_statuses' => array(
                    'type' => 'order_statuses'
                ),
            );
        }

        public function process_payment($order_id)
        {
            global $woocommerce;

            $order = new WC_Order($order_id);
            $address = $order->billing_email;
            $total = $order->get_total();
            $subtotal = $order->get_subtotal();
            $currency = $order->get_order_currency();
            $discount = $order->get_total_discount();
            $shipping_amount = 0;
            if ($order->get_shipping_total() > 0 && $order->get_shipping_total() < 999.99 && $order->get_shipping_total() + $order->get_shipping_tax() !== $order->get_total()) {
                $shipping_amount = $order->get_shipping_total();
            }

            $order_items = $this->order_items($order);

            $key = get_post_meta($order->get_id(), 'thebigcoin_order_key', true);
            if ($key == '') {
                $key = substr(md5('tbc_order_key' . rand()), 0, 32);
                update_post_meta($order_id, 'thebigcoin_order_key', $key);
            }

            $tbcClient = new \TheBigCoinClient\TheBigCoinClient(
                array(
                    'project_id' => $this->project_id,
                    'api_key' => $this->api_key,
                    'api_secret' => $this->api_secret,
                    'env' => ($this->test_mode ? 'test' : 'live'),
                    'user_agent' => 'TheBigCoin/Payment Plugin: ' . TBC_WOOCOMMERCE_PAYMENT_VERSION . '/' . 'WooCommerce: ' . WOOCOMMERCE_VERSION
                )
            );

            $payment = $tbcClient->addPayment(
                array(
                    'order_id' => $order->get_id(),
                    'currency' => $currency,
                    'amount' => $this->formatCurrency($total),
                    'items_amount' => $this->formatCurrency($subtotal),
                    'shipping_amount' => $this->formatCurrency($shipping_amount),
                    'discount_amount' => $this->formatCurrency($discount),
                    'buyer_email' => isset($order->billing_email) ? $order->billing_email : '',
                    'callback_url' => trailingslashit(get_bloginfo('wpurl')) . '?wc-api=wc_gateway_thebigcoin&key=' . $key,
                    'complete_url' => esc_url_raw($this->get_return_url($order)),
                    'cancel_url' => esc_url_raw($order->get_cancel_order_url_raw()),
                    'items' => $order_items
                )
            );

            if ($payment && isset($payment['data']) && !empty($payment['data']) && $payment['data']['payment_url'] && $payment['data']['payment_status'] == 'awaiting_payment') {
                return array(
                    'result' => 'success',
                    'redirect' => $payment['data']['payment_url'],
                );
            } else {
                return array(
                    'result' => 'fail',
                );
            }
        }

        public function callback()
        {
            if ($this->enabled != 'yes') {
                return;
            }

            $request = $_REQUEST;
            if (true === empty($request)) {
                $this->log('[Error] Invalid Request Data' . $request);
                wp_die('Invalid Request data');
            }

            if (false === isset($request['id'])) {
                $this->log('[Error] No Payment ID');
                wp_die('No Payment ID');
            }

            if (false === $request['order_id']) {
                $this->log('[Error] No Order ID');
                wp_die('No Order ID');
            }

            $order_id = $request['order_id'];
            $order_id = apply_filters('woocommerce_order_id_from_number', $order_id);
            $order = wc_get_order($order_id);

            if (false === $order || 'WC_Order' !== get_class($order)) {
                $this->log('[Error] Could not retrieve the order details for order_id ' . $order_id);
                throw new \Exception('Could not retrieve the order details for order_id ' . $order_id);
            }

            $key = get_post_meta($order_id, 'thebigcoin_order_key', true);

            if (false === isset($key) || empty($key) || $_GET['key'] != $key) {
                $this->log('[Error] Order Key Not Found' . $key);
                throw new Exception('Order Key Not Found');
            }

            $tbcClient = new \TheBigCoinClient\TheBigCoinClient(
                array(
                    'project_id' => $this->project_id,
                    'api_key' => $this->api_key,
                    'api_secret' => $this->api_secret,
                    'user_agent' => 'TheBigCoin/Payment Plugin: ' . TBC_WOOCOMMERCE_PAYMENT_VERSION . '/' . 'WooCommerce: ' . WOOCOMMERCE_VERSION
                )
            );

            try {
                $payment = $tbcClient->getPayment($request['id']);
                if (false === isset($payment) || !$payment || empty($payment)) {
                    $this->log('[Error] API - Payment Not Found, Paymend ID:' . $request['id']);
                    wp_die('Payment Not Found');
                }
            } catch (\Exception $e) {
                wp_die($e->getMessage());
            }

            $payment_status = $payment['data']['payment_status'];
            if (false === isset($payment_status) && true === empty($payment_status)) {
                $this->log('[Error] Could not obtain the current status from the payment #' . $payment['data']['id']);
                throw new \Exception('Could not obtain the current status from the payment.');
            } else {
                $this->log('[Info] The current order status for this payment #' . $payment['data']['id'] . ' is ' . $payment_status);
            }

            $order_statuses = $this->get_option('order_statuses');
            $woocomerce_order_status = $order_statuses[$payment['data']['payment_status']];
            $completed_status = $order_statuses['competed'];
            $confirmed_status = $order_statuses['confirmed'];

            $current_status = $order->get_status();
            if (false === isset($current_status) && true === empty($current_status)) {
                $this->log('[Error] Could not obtain the current status from the order #' . $order_id);
                throw new \Exception('Could not obtain the current status from the order #' . $order_id);
            } else {
                $this->log('[Info] The current order status for this order #' . $order_id . ' is ' . $current_status);
            }

            switch ($payment['data']['payment_status']) {
                case 'completed':
                    if ($current_status == $completed_status || 'wc_' . $current_status == $completed_status) {
                        $this->log('[Warning] Order # ' . $order_id . ' has status: ' . $current_status);
                    } else {
                        $order->update_status($woocomerce_order_status);
                        $order->add_order_note(__('TheBigCoin: Payment completed.', 'thebigcoin'));
                    }
                    break;
                case 'confirmed':
                    if ($current_status == $confirmed_status || 'wc_' . $current_status == $confirmed_status) {
                        $this->log('[Warning] Order # ' . $order_id . ' has status: ' . $current_status);
                    } else {
                        $order->payment_complete();
                        $order->add_order_note(__('TheBigCoin: Payment confirmed. Awaiting network confirmation and payment completed status.', 'thebigcoin'));
                        $order->update_status($woocomerce_order_status);
                    }
                    break;
                case 'underpayment':
                    $this->log('[Info] Order #' . $order_id . 'Update Status - Underpayment');
                    $order->add_order_note(__('TheBigCoin: Payment has been underpaid.', 'thebigcoin'));
                    break;
                case 'refunded':
                    $this->log('[Info] Order #' . $order_id . 'Update Status - Refunded');
                    $order->update_status($woocomerce_order_status);
                    $order->add_order_note(__('TheBigCoin: Payment was refunded.', 'thebigcoin'));
                    break;
                case 'invalid':
                    $this->log('[Info] Order #' . $order_id . 'Update Status - Invalid');
                    $order->update_status($woocomerce_order_status);
                    $order->add_order_note(__('TheBigCoin: Payment is invalid for this order.', 'thebigcoin'));
                    break;
                case 'expired':
                    $this->log('[Info] Order #' . $order_id . 'Update Status - Expired');
                    $order->update_status($woocomerce_order_status);
                    $order->add_order_note(__('TheBigCoin: Payment has expired.', 'thebigcoin'));
                    break;
                case 'canceled':
                    $this->log('[Info] Order #' . $order_id . 'Update Status - Canceled');
                    $order->update_status($woocomerce_order_status);
                    $order->add_order_note(__('TheBigCoin: Payment was canceled.', 'thebigcoin'));
                    break;
            }
        }

        public function generate_order_statuses_html()
        {
            ob_start();
            $thebigcoin_statuses = $this->thebigcoin_statuses();
            $woocommerce_statuses = wc_get_order_statuses();
            $default = array(
                'completed' => 'wc-processing',
                'confirmed' => 'wc-processing',
                'underpayment' => 'wc-pending',
                'refunded' => 'wc-refunded',
                'expired' => 'wc-cancelled',
                'canceled' => 'wc-cancelled',
                'invalid' => 'wc-failed'
            );
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">Order Statuses:</th>
                <td id="order_statuses">
                    <table cellspacing="0">
                        <?php
                        foreach ($thebigcoin_statuses as $status_name => $status_title) {
                            ?>
                            <tr>
                                <th><?= $status_title; ?></th>
                                <td>
                                    <select name="woocommerce_thebigcoin_order_statuses[<?= $status_name; ?>]">
                                        <?php
                                        $statuses = get_option('woocommerce_thebigcoin_settings');
                                        $statuses = $statuses['order_statuses'];
                                        foreach ($woocommerce_statuses as $woocommerce_status_name => $woocommerce_status_title) {
                                            $current = $statuses[$status_name];
                                            if (empty($current) === true) {
                                                $current = $default[$status_name];
                                            }
                                            if ($current == $woocommerce_status_name) {
                                                echo "<option value=\"" . $woocommerce_status_name . "\" selected>" . $woocommerce_status_title . "</option>";
                                            } else {
                                                echo "<option value=\"" . $woocommerce_status_name . "\">" . $woocommerce_status_title . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }

        public function validate_order_statuses_field()
        {
            $order_statuses = $this->get_option('order_statuses');
            if (isset($_POST[$this->plugin_id . $this->id . '_order_statuses'])) {
                $order_statuses = $_POST[$this->plugin_id . $this->id . '_order_statuses'];
            }
            return $order_statuses;
        }

        public function save_statuses()
        {
            $thebigcoin_statuses = $this->thebigcoin_statuses();
            $woocommerce_statuses = wc_get_order_statuses();
            if (isset($_POST['woocommerce_thebigcoin_order_statuses']) === true) {
                $settings = get_option('woocommerce_thebigcoin_settings');
                $order_statuses = $settings['order_statuses'];
                foreach ($thebigcoin_statuses as $thebigcoin_status_name => $thebigcoin_status_title) {
                    if (isset($_POST['woocommerce_thebigcoin_order_statuses'][$thebigcoin_status_name]) === false) {
                        continue;
                    }
                    $woocommerce_status_name = $_POST['woocommerce_thebigcoin_order_statuses'][$thebigcoin_status_name];
                    if (array_key_exists($woocommerce_status_name, $woocommerce_statuses) === true) {
                        $order_statuses[$thebigcoin_status_name] = $woocommerce_status_name;
                    }
                }
                $settings['order_statuses'] = $order_statuses;
                update_option('woocommerce_thebigcoin_settings', $settings);
            }
        }

        private function thebigcoin_statuses()
        {
            return array(
                'completed' => 'Completed',
                'confirmed' => 'Confirmed',
                'underpayment' => 'Underpayment',
                'refunded' => 'Refunded',
                'expired' => 'Expired',
                'canceled' => 'Canceled',
                'invalid' => 'Invalid'
            );
        }

        private function order_items($order)
        {
            $items = array();
            $calculated_total = 0;

            foreach ($order->get_items(array('line_item', 'fee')) as $item) {
                if ('fee' === $item['type']) {
                    $item_line_total = $this->formatCurrency($item['line_total']);
                    $line_item = $this->add_order_item($item->get_name(), 1, $item_line_total);
                    $calculated_total += $item_line_total;
                } else {
                    $product = $item->get_product();
                    $sku = $product ? $product->get_sku() : '';
                    $item_line_total = $this->formatCurrency($order->get_item_subtotal($item, false));
                    $line_item = $this->add_order_item($this->get_item_name($order, $item), $item->get_quantity(), $item_line_total, $sku);
                    $calculated_total += $item_line_total * $item->get_quantity();
                }
                if ($line_item) {
                    $items[] = $line_item;
                }
            }

            if ($calculated_total + $order->get_total_tax() + $order->get_shipping_total() - $order->get_total_discount() != $order->get_total()) {
                return false;
            } else {
                return $items;
            }
        }

        protected function add_order_item($item_name, $quantity = 1, $amount = 0.0, $item_number = '')
        {

            $item = array(
                'name' => $this->limit_length(html_entity_decode(wc_trim_string($item_name ? $item_name : __('Item', 'woocommerce'), 127), ENT_NOQUOTES, 'UTF-8'), 127),
                'qty' => (int)$quantity,
                'amount' => wc_float_to_string((float)$amount),
                'item_number' => $item_number,
            );
            return $item;
        }

        protected function get_item_name($order, $item)
        {
            $item_name = $item->get_name();
            $item_meta = strip_tags(wc_display_item_meta($item, array(
                'before' => "",
                'separator' => ", ",
                'after' => "",
                'echo' => false,
                'autop' => false,
            )));

            if ($item_meta) {
                $item_name .= ' (' . $item_meta . ')';
            }
            return $item_name;
        }

        protected function limit_length($string, $limit = 127)
        {
            if (strlen($string) > $limit) {
                $string = substr($string, 0, $limit - 3) . '...';
            }
            return $string;
        }

        private function log($message)
        {
            if (false === isset($this->logger) || true === empty($this->logger)) {
                $this->logger = new WC_Logger();
            }
            $this->logger->add('thebigcoin', $message);
        }

        private function formatCurrency($amount)
        {
            return number_format($amount, 6, '.', '');
        }
    }

    function thebigcoin_gateway_class($methods)
    {
        $methods[] = 'WC_Gateway_Thebigcoin';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'thebigcoin_gateway_class');
}

