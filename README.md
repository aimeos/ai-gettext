<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

Aimeos Gettext extension
===============================
[![Build Status](https://travis-ci.org/aimeos/ai-gettext.svg?branch=master)](https://travis-ci.org/aimeos/ai-gettext)
[![Coverage Status](https://coveralls.io/repos/aimeos/ai-gettext/badge.svg?branch=master)](https://coveralls.io/r/aimeos/ai-gettext?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/ai-gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/ai-gettext/?branch=master)

The Aimeos Gettext extension contains a low resource implementation for reading Gettext MO files and a translation adapter for Aimeos.

## Table of contents

- [Installation](#installation)
- [Usage](#usage)
- [License](#license)
- [Links](#links)

## Installation

As every Aimeos extension, the easiest way is to install it via [composer](https://getcomposer.org/). If you don't have composer installed yet, you can execute this string on the command line to download it:
```
php -r "readfile('https://getcomposer.org/installer');" | php -- --filename=composer
```

Add the Gettext extension name to the "require" section of your ```composer.json``` (or your ```composer.aimeos.json```, depending on what is available) file:
```
"require": [
    "aimeos/ai-gettext": "dev-master",
    ...
],
```

Afterwards you only need to execute the composer update command on the command line:
```
composer update
```

These commands will install the Aimeos extension into the extension directory and it will be available immediately.

## Usage

The Aimeos Gettext adapter is useful for developers writing integrations for applications or frameworks. It's an alternative for the translation objects stored in the Aimeos context. You can instantiate the translation object via
```
$i18n = new \Aimeos\MW\Translation\Gettext( array(<i18n directories>), <locale> );
```

## License

The Aimeos gettext extension is licensed under the terms of the LGPLv3 Open Source license and is available for free.

## Links

* [Web site](https://aimeos.org/)
* [Documentation](https://aimeos.org/docs)
* [Help](https://aimeos.org/help)
* [Issue tracker](https://github.com/aimeos/ai-gettext/issues)
* [Source code](https://github.com/aimeos/ai-gettext)
