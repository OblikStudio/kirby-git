<?php

namespace Oblik\KirbyGit;

load([
	'Oblik\\KirbyGit\\Git' => 'Git.php'
], __DIR__);

return [
	'sections' => [
		'git' => [
			'props' => [
				'headline' => function ($headline = 'Git') {
					return $headline;
				}
			]
		]
	],
	'api' => [
		'routes' => [
			[
				'pattern' => 'git/status',
				'action' => function () {
					return (new Git)->status();
				}
			],
			[
				'pattern' => 'git/add',
				'method' => 'post',
				'action' => function () {
					return (new Git)->add();
				}
			]
		]
	]
];
