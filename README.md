globposer
=========

Composer is a great tool, but let's face it: Its idea of "global" is a bit different from what you might expect: Installing something globally puts it in Composer's home directory, but nowhere in your `PATH`, like good old PEAR did.

globposer does just that: It automatically symlinks the bin files of "globally" installed into `/usr/local/bin`. This location is hardcoded ATM, but it could be made configurable with relatively little effort.

There are of course some limitations: It does not work on Windows currently, and normal Unix file permissions still apply. So if you are installing something as root, Composer's home directory will be `/root/.composer`, which is by default not readable, and thus the links in the `PATH` will not be available to other users on the same machine (like, say, a CI server agent).

Installation
------------

### From git

Clone this repo, change into the main directory, and run composer:
```
git clone https://github.com/flack/globposer.git
cd globposer
php composer.phar install
```
Afterwards, `globposer` will be available in your `PATH`. It may seem a bit cumbersome to keep the git repo around, until you consider the alternative:

### Via Composer

First, you must add `"minimum-stability": "alpha"` to `composer.json` in Composer's home directory (typically `/home/[username]/.composer` on Linux). If the file doesn't exist yet, create one with this content:

```
{
    "minimum-stability": "alpha",
    "require": {
    }
}
```

Ironically, this is necessary because Composer's current release (`1.0.0-alpha9`) does not meet its own default `minimum-stability` requirement of `stable`..

Afterwards, you can use Composer to install globposer globally:

```
php composer.phar global require openpsa/globposer:dev-master
```

You will have to link `globposer` into your `PATH` yourself, because there is no way to do this automatically without having a dedicated installer package, which would be doable, but seems a bit excessive right now.

Usage
-----

globposer is a simple wrapper around Composer's main application file, so the command line syntax and all behavior is exactly the same. globposer only activates when you use the `global` command. This line for example will install PHPUnit and make `phpunit` available in `PATH`:

```
globposer global require phpunit/phpunit
```

This line will uninstall PHPUnit and remove the link:

```
globposer global remove phpunit/phpunit
```
