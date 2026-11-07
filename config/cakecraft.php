<?php

return [
    // Which branch's catalogue the public storefront shows (defaults to the first branch)
    'branch_id' => env('CAKECRAFT_BRANCH_ID'),

    // Optional: only serve the public site on this domain (e.g. www.cakecraft.co.ke). Empty = public site at "/".
    'store_domain' => env('CAKECRAFT_STORE_DOMAIN'),

    'business_name' => env('CAKECRAFT_NAME', 'Cakecraft Pastry Academy'),
    'whatsapp' => env('CAKECRAFT_WHATSAPP', '254700000000'), // international format, no +
    'tagline' => env('CAKECRAFT_TAGLINE', 'Handcrafted cakes for birthdays, weddings and every celebration in between.'),
    'email' => env('CAKECRAFT_EMAIL', ''),
    'logo' => env('CAKECRAFT_LOGO', 'images/cakecraft-logo.jpg'), // path inside /public
    'primary_color' => env('CAKECRAFT_COLOR', '#b76e79'),
    'phone' => env('CAKECRAFT_PHONE', '+254 700 000 000'),
    'address' => env('CAKECRAFT_ADDRESS', 'Nairobi, Kenya'),
];
