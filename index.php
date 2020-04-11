<?php

use Kirby\Cms\App;

if (class_exists('Kirby\Cms\App')) {
	App::plugin('oblik/git', require 'lib/index.php');
} else {
	require_once 'kirby/bootstrap.php';

	$app = new App([
		'roots' => [
			'content' => __DIR__ . '/repo'
		]
	]);

	echo $app->render();
}
