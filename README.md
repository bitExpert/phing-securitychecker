phing-securitychecker
=====================

A Phing task for interacting with the SensioLabs Security Advisories Checker to check if your application uses 
dependencies with known security vulnerabilities.

[![Build Status](https://travis-ci.org/bitExpert/phing-securitychecker.svg?branch=master)](https://travis-ci.org/bitExpert/phing-securitychecker)
[![Dependency Status](https://www.versioneye.com/user/projects/57d9b5111b70a7003f25a522/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57d9b5111b70a7003f25a522)
[![Coverage Status](https://coveralls.io/repos/github/bitExpert/phing-securitychecker/badge.svg?branch=master)](https://coveralls.io/github/bitExpert/phing-securitychecker?branch=master)

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
