<?php

namespace Oblik\KirbyGit;

function array_filter_recursive($input)
{
	foreach ($input as &$value) {
		if (is_array($value)) {
			$value = array_filter_recursive($value);
		}
	}

	return array_filter($input);
}

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
				'auth' => false,
				'action' => function () {
					$code = null;
					$output = [];

					exec('git status -u --porcelain 2>&1', $output, $code);

					$data = [
						'changes' => count($output),
						'staged' => [
							'added' => [],
							'modified' => [],
							'deleted' => []
						],
						'unstaged' => [
							'untracked' => [],
							'modified' => [],
							'deleted' => []
						]
					];

					foreach ($output as $change) {
						$matches = null;

						if (preg_match('!(.{2}) (.+)!', $change, $matches)) {
							$type = $matches[1];
							$file = $matches[2];

							switch ($type) {
								case 'A ':
									$data['staged']['added'][] = $file;
									break;
								case 'M ':
									$data['staged']['modified'][] = $file;
									break;
								case 'D ':
									$data['staged']['deleted'][] = $file;
									break;
								case ' M':
									$data['unstaged']['modified'][] = $file;
									break;
								case ' D':
									$data['unstaged']['deleted'][] = $file;
									break;
								default:
									$data['unstaged']['untracked'][] = $file;
							}
						}
					}

					return array_filter_recursive($data);
				}
			],
			[
				'pattern' => 'git/add',
				'action' => function () {
					$code = null;
					$output = [];

					exec('git add . --verbose 2>&1', $output, $code);

					return compact('code', 'output');
				}
			]
		]
	]
];
