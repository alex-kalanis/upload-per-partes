# Upload Per-Partes

[![Build Status](https://app.travis-ci.com/alex-kalanis/upload-per-partes.svg?branch=master)](https://app.travis-ci.com/github/alex-kalanis/upload-per-partes)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/upload-per-partes/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/upload-per-partes)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/upload-per-partes.svg?v1)](https://packagist.org/packages/alex-kalanis/upload-per-partes)
[![License](https://poser.pugx.org/alex-kalanis/upload-per-partes/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/upload-per-partes)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/?branch=master)

Uploading files via HTTP style per-partes

Contains libraries for uploading large files with JavaScript FileApi
with things like real truth-telling progress bar or available upload resume.

This is the mixed package - contains sever-side implementation in Python and PHP.

## PHP Installation

```
{
    "require": {
        "alex-kalanis/upload-per-partes": "2.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Connect the "kalanis\UploadPerPartes\Upload" into your app. When it came necessary
you can extends every library to comply your use-case; mainly your storage and
processing.

3.) Copy and connect the frontend library "uploader.ts" into your app. You need
something like Grunt to translate TypeScript into JavaScript. You can also extends
included classes to comply your use-case; mainly for styling.

## Python Installation

into your "setup.py":

```
    install_requires=[
        'kw_upload',
    ]
```

## Python Usage

1.) Connect the "kw_upload\upload" into your app. When it came necessary
you can extends every library to comply your use-case; mainly your storage and
processing.

3.) Copy and connect the frontend library "uploader.ts" into your app. You need
something like Grunt to translate TypeScript into JavaScript. You can also extends
included classes to comply your use-case; mainly for styling.
