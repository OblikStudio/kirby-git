# Kirby Git

Shows you Git changes in the Kirby panel and allows you to add/commit/push them, manually or automatically.

![demo gif in the panel](demo.gif)

# Installation

With [Composer](https://packagist.org/packages/oblik/kirby-git):

```
composer require oblik/kirby-git
```

# Usage

The plugin expects a repo to already be initialized and set up. You just give a path to that repo, a branch, and a remote.

## Config

Options for the plugin are specified with dot notation in `site/config/config.php`. For example:

```php
return [
    'oblik.git.repo' => '/path/to/repo',
    'oblik.git.branch' => 'master',
    ...
];
```

### repo

Path to a folder containing a Git repo (a `.git` folder).

**Default:** `kirby()->root('index')` (the project folder)

### branch

What branch to be used for showing commit logs. Setting it to `foo` means `git log` will compare the local branch `foo` with the remote branch `origin/foo`, if the remote is configured to be `origin`.

**Default:** `master`

### remote

What remote to use for `git log` and `git push`.

**Default:** `origin`

### hooks

An array of [Kirby hooks](https://getkirby.com/docs/reference/plugins/extensions/hooks) to use as a trigger for `git add` and `git commit`. Example:

```php
return [
    'oblik.git.hooks' => [
        'site.update:after',
        'page.update:after'
    ]
];
```

With the above config, a new commit will be created any time a page or the site object is updated. However, this could bloat your repository with hundreds (or even thousands) of commits. You could use a hook like [`user.login:after`](https://getkirby.com/docs/reference/plugins/hooks/user-login-after) that gets triggered more rarely.

**Default:** `null`

### log

Absolute path to a file where each command executed by this plugin will be logged. Example:

```php
return [
    'oblik.git.log' => '/path/to/kirby-git.log'
];
```

Logs will look like this:

```
git -C /var/www/site status -u --porcelain 2>&1
git -C /var/www/site rev-list --count master 2>&1
git -C /var/www/site log origin/master..master --format=%h 2>&1
git -C /var/www/site log master --pretty=format:"%h|%an|%ae|%ad|%s" --skip="0" --max-count="15" 2>&1
...
```

**Default:** `false`

## Section

For displaying a summary of Git changes, you could use the `git` section in a blueprint:

```yml
title: Page
sections:
  changes:
    type: git
    headline: Git Status
```

![git status section](section.jpg)

## View

In the panel view, you can add, commit, and push changes in a very simple manner. Just three columns and three buttons:

![git panel view](view.png)

## REST API

The plugin uses Kirby's REST API to provide a means for the panel view to communicate with PHP. You can use it as well! Check the various routes [here](../lib/routes.php).

## API

You can work with Git via the [`Git`](../lib/Git.php) class as well. For example:

```php
use Oblik\KirbyGit\Git;

$git = new Git();
$git->add();
$git->commit('my message');
$git->push();
```