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
	public $branch = null;
	public $remote = null;

	public function __construct()
	{
		$repo = option('oblik.git.repo') ?? getcwd();

		$this->repo = realpath($repo);
		$this->branch = option('oblik.git.branch');
		$this->remote = option('oblik.git.remote');

		if ($this->repo === false) {
			throw new Exception('Inavlid repo path');
		}

		if (getcwd() !== $this->repo) {
			chdir($this->repo);
		}
	}

	public function exec(string $command, array $params = [])
	{
		$code = null;
		$output = [];

		$cmd = 'git';

		if ($name = $params['name'] ?? null) {
			$cmd .= " -c user.name=$name";
		}

		if ($email = $params['email'] ?? null) {
			$cmd .= " -c user.email=$email";
		}

		$cmd .= " $command 2>&1";

		exec($cmd, $output, $code);

		if ($code !== 0) {
			throw new Exception(implode("\n", $output));
		}

		return $output;
	}

	public function status()
	{
		$output = $this->exec('status -u --porcelain');

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
		return $this->exec('add . --verbose');
	}

	public function commit(string $name, string $email, string $message)
	{
		if (count($this->exec('commit --dry-run --porcelain')) === 0) {
			throw new Exception('Nothing to commit');
		}

		$message = escapeshellarg($message);

		return $this->exec("commit --message=$message --no-status", [
			'name' => escapeshellarg($name),
			'email' => escapeshellarg($email)
		]);
	}

	public function log(int $page, int $limit)
	{
		$branch = $this->branch;
		$remote = $this->remote;

		$list = $this->exec("rev-list --count $branch");
		$count = (int) ($list[0] ?? 0);

		$new = $this->exec("log $remote/$branch..$branch --format=%h");

		$format = '"%h|%an|%ae|%ad|%s"';
		$skip = escapeshellarg(($page - 1) * $limit);
		$limit = escapeshellarg($limit);
		$commits = $this->exec("log $branch --pretty=format:$format --skip=$skip --max-count=$limit");

		foreach ($commits as $i => $str) {
			$data = str_getcsv($str, '|');
			$hash = $data[0];

			$commits[$i] = [
				'new' => in_array($hash, $new) !== false,
				'hash' => $hash,
				'name' => $data[1],
				'email' => $data[2],
				'date' => $data[3],
				'subject' => $data[4]
			];
		}

		return [
			'total' => $count,
			'new' => count($new),
			'commits' => $commits
		];
	}

	public function push()
	{
		$branch = $this->branch;
		$remote = $this->remote;

		return $this->exec("push $remote $branch");
	}
}
