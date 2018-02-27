<?php

if (!function_exists('e')) {
    function e(string $s):string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
    }
}
