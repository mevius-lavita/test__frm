<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$host = env('MAIL_HOST', 'smtp.mailtrap.io');
$port = env('MAIL_PORT', 2525);
$user = env('MAIL_USERNAME');
$pass = env('MAIL_PASSWORD');
echo "Host: $host\nPort: $port\nUser: " . ($user ? 'set' : 'empty') . "\n";
$fp = @fsockopen($host, $port, $errno, $errstr, 10);
if (!$fp) {
    echo "Connection failed: $errno $errstr\n";
    exit(1);
}
stream_set_timeout($fp, 10);
function readResp($fp)
{
    $s = '';
    while (($line = fgets($fp, 515)) !== false) {
        $s .= $line;
        if (isset($line[3]) && $line[3] == ' ') {
            break;
        }
    }
    return $s;
}
$banner = readResp($fp);
echo "Banner:\n$banner\n";
fputs($fp, "EHLO localhost\r\n");
$ehlo = readResp($fp);
echo "EHLO response:\n$ehlo\n";
if (strpos($ehlo, 'STARTTLS') !== false) {
    echo "Server supports STARTTLS\n";
}
if ($user) {
    fputs($fp, "AUTH LOGIN\r\n");
    $r = readResp($fp);
    echo "AUTH LOGIN prompt:\n$r\n";
    fputs($fp, base64_encode($user) . "\r\n");
    $r = readResp($fp);
    echo "After user (base64) response:\n$r\n";
    fputs($fp, base64_encode($pass) . "\r\n");
    $r = readResp($fp);
    echo "After pass (base64) response:\n$r\n";
} else {
    echo "No MAIL_USERNAME set, skipping AUTH\n";
}
fputs($fp, "QUIT\r\n");
readResp($fp);
fclose($fp);
