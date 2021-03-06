<?php

require __DIR__ . '/../vendor/autoload.php';

$a = new Simple\Asset\Serve();

$css = array(
           'css/1.css',
           'css/2.css'
           );

$js = array(
           'js/1.js',
           'js/2.js'
           );

$f = array_map(function ($n) {
    return __DIR__ . '/file/'. $n;
}, $js);

echo $a->js($f);
