<?php

use Eurom\Generation\Generator;

require __DIR__ . '/vendor/autoload.php';

require "conf.php";
if (isset($argc) && $argc === 2) {
    $generator = new Generator();
    if ($argv[1] === "generateAll")
        $generator->generateAll(false);
    elseif ($argv[1] === "generateDBObjects")
        $generator->generateDBObjects(false);
    elseif ($argv[1] === "generateTS")
        $generator->generateTS(false);
    elseif ($argv[1] === "generateForced")
        $generator->generateAll(true);
}
