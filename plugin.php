<?php

use Kirby\Cms\App;

App::plugin('oblik/git', [
  'routes' => [
    [
      'pattern' => 'git/status',
      'action' => function () {
        $code = null;
        $output = [];

        exec('git status --porcelain 2>&1', $output, $code);

        return compact('code', 'output');
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
]);
