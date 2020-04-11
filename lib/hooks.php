<?php

namespace Oblik\KirbyGit;

$names = option('oblik.git.hooks');
$hooks = [];

if (is_array($names)) {
	foreach ($names as $name) {
		$hooks[$name] = hook($name);
	}
}

return $hooks;
