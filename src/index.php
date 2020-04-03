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

					$data = array_map(function ($line) {
						$matches = null;
						$data = [];

						if (preg_match('!(.{2}) (.+)!', $line, $matches)) {
							$axes = str_split($matches[1]);
							$data['file'] = $matches[2];

							if ($axes[0] !== ' ') {
								$data['staged'] = $axes[0];
							}

							if ($axes[1] !== ' ') {
								$data['unstaged'] = $axes[1];
							}
						}

						return $data;
					}, $output);

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
