phing-securitychecker
=====================

A Phing task for interacting with the SensioLabs Security Advisories Checker to check if your application uses 
dependencies with known security vulnerabilities.

[![Build Status](https://travis-ci.org/bitExpert/phing-securitychecker.svg?branch=release%2Fr0.1.0)](https://travis-ci.org/bitExpert/phing-securitychecker)

Installation
------------

The preferred way of installation is through Composer. Add `bitexpert/phing-securitychecker` as a dependency to 
composer.json:

```javascript
{
    "require": {
        "bitexpert/phing-securitychecker": "0.1.*stable"
    }
}
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
