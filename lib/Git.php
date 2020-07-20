<?php

namespace Oblik\KirbyGit;

use Exception;

class Git
{
	protected $config;

	/**
	 * Executable to run.
	 */
	protected $bin;

	/**
	 * Absolute path to a Git repo.
	 */
	protected $repo;

	/**
	 * The currently checked out branch in the repo.
	 */
	protected $branchCurrent;

	/**
	 * Branch used to merge in a fast-forward-only fashion.
	 */
	protected $branchMerge;

	/**
	 * Remote used when comparing commits or pushing.
	 */
	protected $remote;

	/**
	 * File where each executed command is logged.
	 */
	protected $logfile;

	public function __construct(array $config = [])
	{
		$this->config = $config;
		$this->bin = $this->option('bin');
		$this->repo = realpath($this->option('repo'));

		if ($this->repo === false) {
			throw new Exception('Inavlid repo path');
		}

		$this->branchCurrent = $this->branch();

		if (empty($this->branchCurrent)) {
			throw new Exception('No checked out branch');
		}

		$this->branchMerge = $this->option('merge');
		$this->remote = $this->option('remote');
		$this->logfile = $this->option('log');
	}

	public function option(string $name)
	{
		return ($this->config[$name] ?? null) ?? (option("oblik.git.$name") ?? null);
	}

	public function exec(string $command)
	{
		$code = null;
		$output = [];
		$cmd = "{$this->bin} -C {$this->repo} {$command} 2>&1";

		if ($this->logfile) {
			file_put_contents($this->logfile, $cmd . PHP_EOL, FILE_APPEND);
		}

		exec($cmd, $output, $code);

		if ($code !== 0) {
			$message = implode(PHP_EOL, $output);
			throw new Exception($this->deduceError($message) ?? $message);
		}

		return $output;
	}

	public function deduceError(string $message)
	{
		if (strpos($message, 'usage: git ') !== false) {
			return 'It seems you have an outdated Git version: ' . $this->version();
		} else if (strpos($message, 'Not possible to fast-forward') !== false) {
			return 'Refusing to pull remote changes because they’re not merged with the local changes.';
		}
	}

	public function version()
	{
		$version = null;

		try {
			$output = [];
			exec('git --version', $output);
			$version = implode('', $output);
		} catch (Exception $e) {
			$version = 'unknown';
		}

		return $version;
	}

	public function branch()
	{
		return $this->exec('branch --show-current')[0] ?? null;
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

	public function commit($message)
	{
		$committer = kirby()->user();
		$name = $committer->name()->value();
		$email = $committer->email();

		if (empty($name) || empty($email)) {
			$name = 'Kirby Git';
			$email = 'hello@oblik.studio';
		}

		$message = escapeshellarg($message);
		$name = escapeshellarg($name);
		$email = escapeshellarg($email);

		return $this->exec("-c user.name={$name} -c user.email={$email} commit --message={$message} --no-status");
	}

	public function log(int $page, int $limit)
	{
		$list = $this->exec("rev-list --count {$this->branchCurrent}");
		$count = (int) ($list[0] ?? 0);

		$new = $this->exec("log {$this->remote}/{$this->branchCurrent}..{$this->branchCurrent} --format=%h");

		$format = '"%h|%an|%ae|%ad|%s"';
		$skip = escapeshellarg(($page - 1) * $limit);
		$limit = escapeshellarg($limit);
		$commits = $this->exec("log {$this->branchCurrent} --pretty=format:{$format} --skip={$skip} --max-count={$limit}");

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
		return $this->exec("push {$this->remote} {$this->branchCurrent}");
	}

	public function pull()
	{
		return $this->exec("pull {$this->remote} {$this->branchMerge} --ff-only");
	}
}
