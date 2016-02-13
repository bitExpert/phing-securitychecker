phing-securitychecker
=====================

A Phing task for interacting with the SensioLabs Security Advisories Checker to check if your application uses 
dependencies with known security vulnerabilities.

[![Build Status](https://travis-ci.org/bitExpert/phing-securitychecker.svg?branch=master)](https://travis-ci.org/bitExpert/phing-securitychecker)
[![Dependency Status](https://www.versioneye.com/php/bitexpert:phing-securitychecker/dev-master/badge.svg)](https://www.versioneye.com/php/bitexpert:phing-securitychecker/dev-master)

Installation
------------

The preferred way of installing `bitexpert/phing-securitychecker` is through Composer. Add `bitexpert/phing-securitychecker` as a dependency to 
composer.json:

```
composer.phar require bitexpert/phing-securitychecker
```

Example
-------

Import the default build.xml to let Phing know about the Security Checker task:

```xml
    <import file="vendor/bitexpert/phing-securitychecker/build.xml" />
```

Or define the securitychecker task on your own:

```xml
     <taskdef name="securitychecker" classname="bitExpert\Phing\SecurityChecker\SecurityCheckerTask" />
```

Call the task from your build target:

```xml
    <securitychecker lockfile="composer.lock" />
```

License
-------

phing-securitychecker is released under the Apache 2.0 license.
