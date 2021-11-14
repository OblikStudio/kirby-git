<?php

namespace PhpCsFixer;

$finder = Finder::create()
	->exclude([
		'.git',
		'kirby',
		'node_modules',
		'vendor'
	])
	->in(__DIR__);

return (new Config())
	->setRules([
		'@PSR12' => true
	])
	->setIndent("\t")
	->setFinder($finder);
