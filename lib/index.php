<?php

namespace Oblik\KirbyGit;

use Exception;

require_once 'util.php';

load([
	'Oblik\\KirbyGit\\Git' => 'Git.php'
], __DIR__);

return [
	'options' => [
		'repo' => getcwd(),
		'branch' => 'master',
		'remote' => 'origin',
		'log' => false
	],
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
					$message = kirby()->request()->data()['message'] ?? null;

					if (empty($message)) {
						throw new Exception('No commit message');
					}

					return (new Git)->commit($name, $email, $message);
				}
			],
			[
				'pattern' => 'git/log',
				'method' => 'get',
				'action' => function () {
					$data = kirby()->request()->data();
					return (new Git)->log($data['page'], $data['limit']);
				}
			],
			[
				'pattern' => 'git/push',
				'method' => 'post',
				'action' => function () {
					return (new Git)->push();
				}
			]
		]
	],
	'hooks' => require 'hooks.php'
];
