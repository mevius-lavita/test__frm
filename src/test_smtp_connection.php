<?php

$hosts = ['sandbox.smtp.mailtrap.io', 'smtp.mailtrap.io'];
foreach ($hosts as $h) {
    $fp = @fsockopen($h, 2525, $errno, $errstr, 5);
    if ($fp) {
        echo "$h: ok\n";
        fclose($fp);
    } else {
        echo "$h: err $errno $errstr\n";
    }
}
