<?php


// Razorpay Secret Key
$razorpay_secret = 'bxuvY2RXjkw6K74mxFuLU4di'; // example: '2bBa1F7K2hwI0UqHgfwT0qg0'

$razorpay_order_id = "order_RXIdWw2m4UVW5e";
$razorpay_payment_id = "pay_RXIdXxYZ123";

$generated_signature = hash_hmac(
    'sha256',
    $razorpay_order_id . '|' . $razorpay_payment_id,
    $razorpay_secret
);

echo $generated_signature;
