<?php

namespace Oblik\KirbyGit;

return [
	[
		'pattern' => 'git/status',
		'method' => 'get',
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
			$data = kirby()->request()->data();
			$message = $data['message'] ?? null;

			return (new Git)->commit($message);
		}
	],
	[
		'pattern' => 'git/log',
		'method' => 'get',
		'action' => function () {
			$data = kirby()->request()->data();
			$page = (int) ($data['page'] ?? 1);
			$limit = (int) ($data['limit'] ?? 50);

			return (new Git)->log($page, $limit);
		}
	],
	[
		'pattern' => 'git/push',
		'method' => 'post',
		'action' => function () {
			return (new Git)->push();
		}
	],
	[
		'pattern' => 'git/pull',
		'method' => 'get',
		'action' => function () {
			return (new Git)->pull();
		}
	]
];
