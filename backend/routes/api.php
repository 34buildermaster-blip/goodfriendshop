<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'game-topup-api',
    ]);
});

Route::get('/games', function () {
    return response()->json([
        [
            'id' => 'ragnarok-m',
            'name' => 'Ragnarok M',
            'publisher' => 'Gravity',
            'status' => 'active',
        ],
        [
            'id' => 'rov',
            'name' => 'ROV',
            'publisher' => 'Garena',
            'status' => 'draft',
        ],
    ]);
});

Route::get('/payment-methods', function () {
    return response()->json([
        ['id' => 'promptpay', 'name' => 'PromptPay', 'status' => 'active'],
        ['id' => 'truemoney', 'name' => 'TrueMoney', 'status' => 'draft'],
        ['id' => 'bank-transfer', 'name' => 'Bank Transfer', 'status' => 'draft'],
    ]);
});
