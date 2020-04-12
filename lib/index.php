<?php

namespace Oblik\KirbyGit;

require_once 'util.php';

load([
	'Oblik\\KirbyGit\\Git' => 'Git.php'
], __DIR__);

return [
	'options' => [
		'repo' => kirby()->root('index'),
		'merge' => 'master',
		'remote' => 'origin',
		'log' => false
	],
	'sections' => [
		'git' => require 'section.php'
	],
	'api' => [
		'routes' => require 'routes.php'
	],
	'hooks' => require 'hooks.php'
];
