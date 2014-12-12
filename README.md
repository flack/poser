globposer
=========

globposer is a modified version of Composer for globally installed
packages. It changes the linking behavior for CLI executables,
so that they are automatically available in `PATH`. Think of it as
a replacement for Composer's `global` command

Composer is a great tool, but let's face it: Its idea of "global" is a
bit different from what you might expect: Installing a package
globally only means it ends up in Composer's home directory, so
to call CLI tools, you still have to type the entire path or create a
symlink to a directory in your `PATH` manually.

globposer does that for you: It automatically symlinks the bin files
of installed packages into `/usr/local/bin`. This means
command-line utilities of Composer packages will be available
in the shell the same way PEAR packages were.

Installation
------------

### From git

Clone this repo, change into the main directory, and run composer:
```bash
git clone https://github.com/flack/globposer.git
cd globposer
php composer.phar install
```
Afterwards, `globposer` will be available in your `PATH`. It may seem
a bit cumbersome to keep the git repo around, but the alternative
currently looks like this:

### Via Composer

First, you must add `"minimum-stability": "alpha"` to `composer.json`
in Composer's home directory (typically `/home/[username]/.composer`
on Linux). If the file doesn't exist yet, create one with this content:

```json
{
    "minimum-stability": "alpha",
    "require": {
    }
}
```

(Ironically, this is necessary because Composer's current release does
not meet its own default `minimum-stability` requirement of `stable`..)

Afterwards, you can use Composer to install globposer globally:

```bash
php composer.phar global require openpsa/globposer:dev-master
```

You will have to link `globposer` into your `PATH` yourself, because
there is no way to do this automatically without having a dedicated
installer package, which would be doable, but seems a bit excessive right now.

Usage
-----

globposer is a simple wrapper around Composer's main application file,
so the command line syntax and all behavior is exactly the same,
except that the `global` command is automatically prepended (which
means that you cannot use globposer to manage project-level
dependencies).

This line for example will install PHPUnit and make `phpunit` available in `PATH`:

```bash
globposer require phpunit/phpunit
```

This line will uninstall PHPUnit and remove the link:

```bash
globposer remove phpunit/phpunit
```

Caveats
-------
globposer is a quickly-written proof of concept, because I expect that the Composer team will at some point come up with an implementation of its behavior directly in the core. This is just meant to hold us over until then, so there are some limitations:

 - `/usr/local/bin` is hardcoded ATM, but it could be made configurable with relatively little effort.
 - Windows is not supported currently. I guess it could be made to work, so pull requests are welcome
 - normal Unix file permissions still apply. So if you are installing something as root, Composer's home directory will be `/root/.composer`, which is by default not readable, and thus the links in the `PATH` will not be available to other users on the same machine (like, say, a CI server agent).
 - if you uninstall globposer (via composer or by deleting the git repo), you will have to remove the `globposer` symlink in `/usr/local/bin` manually
