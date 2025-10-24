<?php


// Razorpay Secret Key
$razorpay_secret = 'bxuvY2RXjkw6K74mxFuLU4di'; // example: '2bBa1F7K2hwI0UqHgfwT0qg0'

// Order ID and Payment ID (These will be from the response of create order)
$order_id = 'order_RXCNrM8OnHPJ4r';   // example Razorpay order ID
$payment_id = 'b3348365-0727-4f50-80c4-73dc51567cf1';            // example Razorpay payment ID

// Generating the signature
$signature = hash_hmac('sha256', $order_id . '|' . $payment_id, $razorpay_secret);

echo "Generated Signature: " . $signature;
