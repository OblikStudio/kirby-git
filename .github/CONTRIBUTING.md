# Contributing

Thanks a lot for deciding to help with the project, it's much appreciated! 🙏

## Initialization

You should have the following installed on your machine:

- [`git`](https://git-scm.com/) version `^2.22`
- [`npm`](https://www.npmjs.com/) version `^7.0`
- [`composer`](https://getcomposer.org/) version `^2.0`

Then, you should clone the repo and run the [`bash ./scripts/init`](../scripts/init)
script. It will:

1. Install all dependencies
1. Set up a git hook to check for code formatting on commit
1. Create two dummy repositories where you can play around with the plugin

If something doesn't work out, just check what the script does and do it manually.

## Code Formatting

I follow PSR for PHP and Prettier defaults for everything else. The only
exception is the use of spaces. I like tabs for their accessibility,
flexibility, and ease of use. Please, don't switch the project to spaces. I
won't accept it.

- `npm run fix` runs [Prettier](https://github.com/prettier/prettier) and formats JS/Vue code
- `composer fix` runs [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and formats PHP code

Additionally, there's a [`./scripts/pre-commit`](../scripts/pre-commit) hook
that runs those two tools for you before each commit and prevents you from
committing badly formatted code. You can install it with the
[`./scripts/add-git-hooks`](../scripts/add-git-hooks) script, if the init script
hasn't done that already.

## Testing the Plugin

When you initialize the project, you've actually initialized a fully functioning
Kirby site as well. The main difference is that `index.php` is reserved for the
plugin, while the site is loaded with `index.site.php` and the corresponding
`.htaccess` configuration to do so. This allows you to open the plugin in this
test site's panel and play around with it.

All files related to this test site are added to
[`.gitattributes`](../.gitattributes), so you don't have to worry about them
appearing in the production version of the plugin. They're removed.

### Dummy Repositories

The [`./scripts/create-repos`](../scripts/create-repos) script creates two git
repositories in your working directory:

- `content-origin`, which acts as a remote repository
- `content`, which can push to or pull from the first repository

You can make changes to the `content` repo via the panel and push them to the
other repo using the plugin. Then, you can commit changes in `content-origin`
and pull them in `content` using the plugin again. This way, you can easily test
all functionalities of the plugin.

## Pull Requests

Please, make pull requests to the `dev` branch and split multiple fixes/features
in separate PRs.

---

**Thank You!**
