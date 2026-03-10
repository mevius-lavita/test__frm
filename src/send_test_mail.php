<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('テスト送信', function ($m) {
        $m->to('test@example.com')->subject('Mailtrap テスト');
    });
    echo "Mail send attempted\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
