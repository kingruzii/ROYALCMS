<?php
/**
 * Handles Stripe Checkout Session redirects or Mock Sandbox redirects
 */
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 50;
    $donor_name = isset($_POST['donor_name']) ? trim($_POST['donor_name']) : 'Anonymous';
    $donor_email = isset($_POST['donor_email']) ? trim($_POST['donor_email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($amount < 1 || empty($donor_email)) {
        header('Location: ' . BASE_PATH . '/donate.php?status=error');
        exit;
    }

    $amount_cents = intval($amount * 100);
    $origin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

    // Check if live/test Stripe Key is defined
    if (defined('STRIPE_SECRET_KEY') && !empty(STRIPE_SECRET_KEY)) {
        // Stripe integration using standard PHP curl (no composer dependencies required)
        $stripe_key = STRIPE_SECRET_KEY;
        $url = 'https://api.stripe.com/v1/checkout/sessions';

        $post_fields = [
            'payment_method_types[0]' => 'card',
            'line_items[0][price_data][currency]' => 'usd',
            'line_items[0][price_data][product_data][name]' => 'Donation to Royal Village International Foundation',
            'line_items[0][price_data][unit_amount]' => $amount_cents,
            'line_items[0][quantity]' => 1,
            'mode' => 'payment',
            'success_url' => $origin . BASE_PATH . '/donate.php?status=success&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $origin . BASE_PATH . '/donate.php?status=cancelled',
            'customer_email' => $donor_email,
            'metadata[donor_name]' => $donor_name,
            'metadata[donor_email]' => $donor_email,
            'metadata[message]' => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($ch, CURLOPT_USERPWD, $stripe_key . ':');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && !empty($res)) {
            $session = json_decode($res, true);
            if (isset($session['url'])) {
                header('Location: ' . $session['url']);
                exit;
            }
        }
        
        // If Stripe creation fails, fall back to error
        header('Location: ' . BASE_PATH . '/donate.php?status=error');
        exit;
    } else {
        // Sandbox mock flow:
        // Construct mock session id containing payment metadata
        $mock_id = 'mock_' . time() . '__' . $amount . '__' . urlencode($donor_email) . '__' . urlencode($donor_name) . '__' . urlencode($message);
        
        // Wait 1 second to simulate Stripe connection latency
        sleep(1);
        header('Location: ' . BASE_PATH . '/donate.php?status=success&session_id=' . $mock_id);
        exit;
    }
} else {
    header('Location: ' . BASE_PATH . '/donate.php');
    exit;
}
