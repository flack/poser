poser
=========

poser is a proxy for Composer's `global` command that makes CLI executables
automatically available in `PATH`.

Composer is a great tool, but let's face it: Its idea of "global" is a
bit different from what you might expect: Installing a package
globally only means it ends up in Composer's home directory, so
to call CLI tools, you still have to type the entire path or create a
symlink to a directory in your `PATH` manually.

poser does that for you: It automatically symlinks the bin files
of installed packages into `/usr/local/bin`. This means
command-line utilities of Composer packages will be available
in the shell the same way PEAR packages were.

Usage
-----

The command line syntax and all behavior is exactly as they are in Composer,
except that the `global` command is automatically prepended:

```bash
# Install PHPUnit and create symlink for phpunit
poser require phpunit/phpunit

# Install PHPUnit and create symlink for phpunit
poser remove phpunit/phpunit

# You can use other commands and arguments as in Composer
poser show --installed
```

Installation
------------

### From git

Clone this repo, change into the main directory, and run composer:
```bash
git clone https://github.com/flack/poser.git
cd poser
php composer.phar install
```
Afterwards, `poser` will be available in your `PATH`. It may seem
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

Afterwards, you can use Composer to install poser globally:

```bash
php composer.phar global require openpsa/poser:dev-master
```

You will have to link `poser` into your `PATH` yourself, because
there is no way to do this automatically without having a dedicated
installer package, which would be doable, but seems a bit excessive right now.

Caveats
-------
poser is a quickly-written proof of concept, because I expect that the Composer team will at some point come up with an implementation of its behavior directly in the core. This is just meant to hold us over until then, so there are some limitations:

 - `/usr/local/bin` is hardcoded ATM, but it could be made configurable with relatively little effort.
 - Windows is not supported currently. I guess it could be made to work, so pull requests are welcome
 - normal Unix file permissions still apply. So if you are installing something as root, Composer's home directory will be `/root/.composer`, which is by default not readable, and thus the links in the `PATH` will not be available to other users on the same machine (like, say, a CI server agent).
 - if you uninstall poser (via composer or by deleting the git repo), you will have to remove the `poser` symlink in `/usr/local/bin` manually
