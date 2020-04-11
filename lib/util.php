<?php

namespace Oblik\KirbyGit;

use Exception;
use Kirby\Cms\Model;

function array_filter_recursive($input)
{
	foreach ($input as &$value) {
		if (is_array($value)) {
			$value = array_filter_recursive($value);
		}
	}

	return array_filter($input);
}

function hook(string $name)
{
	return function ($input) use ($name) {
		$git = new Git();
		$message = $name;

		if (is_a($input, Model::class)) {
			$message .= ' ' . $input->id();
		}

		try {
			$git->add();
			$git->commit($message);
		} catch (Exception $e) {}
	};
}
