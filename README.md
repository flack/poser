poser
=========

poser is an alternative implementation of Composer's `global` command 
that installs packages in a system-wide location. CLI executables from 
packages are automatically available in `PATH`, just like they were in PEAR.

Composer is a great tool, but let's face it: Its `global` command is
slightly mis-named: Packages installed with it end up in Composer's 
home directory, which is derived from the current user's home directory.
So "global" here really means user-level as opposed to system-level, which
is what PEAR did.

poser attempts to bridge that gap: It installs packages into a single 
location (`/usr/local/share/poser`) regardless of which user triggered the 
command, and it automatically symlinks the bin files
of installed packages into `/usr/local/bin`. This means
command-line utilities of Composer packages will be available
in the shell immediately to all users, without any fiddling in `/etc/profile` 
or other fun places in the bowels of the OS.

Usage
-----

The command line syntax and all behavior is exactly as they are in Composer,
except that the `global` command is currently forbidden. Not so much because
it would be impossible to implement, but rather because it doesn't really make
much sense to use poser for that. It is meant as a specialised version of
Composer for packages you want to install system-wide:

```bash
# Install PHPUnit and create symlink for phpunit
poser require phpunit/phpunit

# Uninstall PHPUnit and remove symlink
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
Afterwards, `poser` will be available in your `PATH` and you can start installing
packages.

It may seem a bit cumbersome to keep the git repo around, but the alternative
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
poser is a quickly-written proof of concept, because I expect that the Composer 
team will at some point come up with an implementation of its behavior directly 
in the core. This is just meant to hold us over until then, so there are 
some limitations:

 - `/usr/local/bin` and `/usr/local/share/poser` are hardcoded ATM (but they could be
   made configurable with relatively little effort)
 - Windows is not supported currently. I guess it could be made to work, so pull 
   requests are welcome
 - obviously, normal Unix file permission rules still apply. So you should make 
   sure that all users that are supposed to use globally installed utilities actually
   have access to `/usr/local/share/poser`
 - if you uninstall poser (via composer or by deleting the git repo), you will have to 
   remove the `poser` symlink in `/usr/local/bin` manually
