<?php

/**
 * https://github.com/getkirby/kirby/blob/947a3158b534362256ee7d1d2474f5dca364782b/src/Cms/App.php#L1061
 * Kirby uses `$_SERVER['SCRIPT_NAME']` and expects this main index file to be
 * named `index.php`, otherwise it throws `NotFoundException`.
 */
$_SERVER['SCRIPT_NAME'] = str_replace('index.site.php', 'index.php', $_SERVER['SCRIPT_NAME']);

require_once 'kirby/bootstrap.php';

echo kirby()->render();
