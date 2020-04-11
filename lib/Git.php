<?php

namespace Oblik\KirbyGit;

use Exception;

class Git
{
	protected $config;

	/**
	 * Absolute path to a Git repository.
	 */
	protected $repo;

	/**
	 * Branch used when displaying commit logs or pushing.
	 */
	protected $branch;

	/**
	 * Remote used when comparing commits or pushing.
	 */
	protected $remote;

	public function __construct(array $config = [])
	{
		$this->config = $config;
		$this->repo = realpath($this->option('repo'));

		if ($this->repo === false) {
			throw new Exception('Inavlid repo path');
		}

		$this->branch = $this->option('branch');
		$this->remote = $this->option('remote');
	}

	public function option(string $name)
	{
		return ($this->config[$name] ?? null) ?? (option("oblik.git.$name") ?? null);
	}

	public function exec(string $command, array $params = [])
	{
		$code = null;
		$output = [];
		$repo = $this->repo;

		$cmd = "git -C $repo";

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
