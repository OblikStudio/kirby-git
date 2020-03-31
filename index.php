<?php

if (!class_exists('Kirby\Cms\App')) {
  require_once 'kirby/bootstrap.php';
  echo (new Kirby)->render();
} else {
  require_once 'plugin.php';
}
