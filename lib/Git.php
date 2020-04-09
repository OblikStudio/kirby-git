<?php

namespace Oblik\KirbyGit;

use Exception;

function array_filter_recursive($input)
{
	foreach ($input as &$value) {
		if (is_array($value)) {
			$value = array_filter_recursive($value);
		}
	}

	return array_filter($input);
}

class Git
{
	public $repo = null;

	public function __construct(string $repo = null)
	{
		if ($repo === null) {
			$repo = option('oblik.git.repo') ?? getcwd();
		}

		$this->repo = realpath($repo);

		if ($this->repo === false) {
			throw new Exception('Inavlid repo path');
		}

		if (getcwd() !== $this->repo) {
			chdir($this->repo);
		}
	}

	public function status()
	{
		$output = [];

		exec('git status -u --porcelain 2>&1', $output);

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

	public function add()
	{
		$output = [];

		exec('git add . --verbose 2>&1', $output);

		return $output;
	}

	public function commit(string $author, string $message) {
		$output = [];

		exec('git commit --dry-run --porcelain', $output);

		if (count($output) === 0) {
			throw new Exception('Nothing to commit');
		}

		$output = [];
		$author = escapeshellarg($author);
		$message = escapeshellarg($message);

		exec("git commit --message=$message --author=$author --no-status", $output);

		return $output;
	}
}
