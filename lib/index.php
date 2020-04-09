<?php

namespace Oblik\KirbyGit;

use Exception;

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
			],
			[
				'pattern' => 'git/commit',
				'method' => 'post',
				'action' => function () {
					$committer = kirby()->user();
					$name = $committer->name()->value() ?? 'Kirby Git';
					$email = $committer->email() ?? 'hello@oblik.studio';

					$author = $name . ' <' . $email . '>';
					$message = kirby()->request()->data()['message'] ?? null;

					if (empty($message)) {
						throw new Exception('No commit message');
					}

					return (new Git)->commit($author, $message);
				}
			]
		]
	]
];
