<?php

namespace Oblik\KirbyGit;

use Kirby\Cms\App;

require_once 'lib/util.php';

load([
	'Oblik\\KirbyGit\\Git' => 'lib/Git.php'
], __DIR__);

App::plugin('oblik/git', [
	'areas' => [
		'git' => function () {
			return [
				'icon' => 'box',
				'menu' => true,
				'views' => [
					[
						'pattern' => 'git',
						'action' => function () {
							return [
								'component' => 'k-git-view',
								'title' => 'Git',
							];
						}
					]
				]
			];
		}
	],
	'options' => [
		'bin' => 'git',
		'repo' => kirby()->root('index'),
		'remote' => 'origin',
		'merge' => 'master',
		'log' => false
	],
	'sections' => [
		'git' => require 'lib/section.php'
	],
	'api' => [
		'routes' => require 'lib/routes.php'
	],
	'hooks' => require 'lib/hooks.php'
]);
