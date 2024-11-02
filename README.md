# Upload Per-Partes

![Build Status](https://github.com/alex-kalanis/upload-per-partes/actions/workflows/code_checks.yml/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/upload-per-partes/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/upload-per-partes)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/upload-per-partes.svg?v1)](https://packagist.org/packages/alex-kalanis/upload-per-partes)
[![License](https://poser.pugx.org/alex-kalanis/upload-per-partes/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/upload-per-partes)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/upload-per-partes/?branch=master)

Uploading files via HTTP style per-partes

Contains libraries for uploading large files with JavaScript FileApi
with things like real truth-telling progress bar or available upload resume.

This is the mixed package - contains sever-side implementation in PHP and
client-side in JavaScript/TypeScript.

## Demonstration

All relevant examples are in ```/examples``` directory. The API between backend
and frontend is described in Laravel and Symphony versions by OpenAPI. Many things
can be set via initial option array as will be described later.

### Principles

The file upload is not something that is so simple as it seems from the user side.
To successfully upload some file you must expect problems with the connection or
processing. This package uses a few important things for achieve its purpose.

1. *Small parts!* The usual upload (especially HTTP) sent the whole file at once
    and you must manipulate the upload process via some side channel. That has some
    disadvantages. Mainly when the upload crashes. Another is unknown passed size or
    upload speed.
2. *Client rules.* The main load of decisions what will be done is on the client side.
    The server is only your servant. That is different from the other style uploads
    where the server has larger say what is possible to upload. Mainly the size.
    This library goes around that problem. The only necessary thing is to store
    the shared key from the server from the previous step.
3. *Indirect.* The upload is indirect. That means the upload is sent to final storage
    AFTER all parts are on the server. The final storage can be either local or remote
    accessible via other libraries/connections. This also affects the manipulation -
    you can safely pause or cancel the process and the destination won't be affected.
4. *Safe transfer.* HTTP wasn't born with binary transfer in mind. That affects
    many things during upload. Modern browsers are usually capable of processing
    files, but the situation server-side is worse. This package can encode each
    part of binary data to achieve a safe transfer. With your own extension it's
    also possible to encrypt data parts to enhance safety.
5. *File-based.* No need to set external DB. You already have the necessary storage.

## PHP Installation

```bash
composer.phar require alex-kalanis/upload-per-partes
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Connect the "kalanis\UploadPerPartes\Upload" into your app. When it came necessary
you can extend every library to comply your use-case; mainly your storage and
processing. All necessary settings are passed via constructor and array of params.

3.) Copy and connect the frontend library "uploader.ts" into your app. You need
something like Grunt to translate TypeScript into JavaScript. Or you can use
"uploader.js" which is Javascript version of the code. You may also extend
included classes to comply your use-case; mainly for styling or paths.

### Configuration options

All configuration is set on initialization by array of params for Uploader class.
All entries can be ignored, although I recommend to set at least "temp_location"
and "target_location".

* **calc_size** - *integer*, sets the size of the block. Is up to you how much you
    allow to pass by each segment.
* **temp_location** - *string*, set the location of directory with temporary files.
    Usually ```/temp``` or somewhere in directory accessible by web scripts
* **target_location** - *string*, the directory where it will be stored AFTER the
    upload will be complete.
* **lang** - *string|object*, the translation of all used quotes, not just when
    something fails. They are usually passed to uploader area on frontend, so
    it's better to use something. The value must be either the instance of
    ```\kalanis\UploadPerPartes\Interfaces\IUppTranslations``` interface
    or class-string with class implementing that interface.
* **target** - *string|object*, where is the local target. This package can be set
    to be chained with more parts. So the client won't be speaking with the real
    storage. This directive sets that possibility. The value must be either
    the instance of ```\kalanis\UploadPerPartes\Interfaces\IOperations``` interface
    or class-string with class implementing that interface or path to some target
    directory/remote URL base.
* **data_encoder** - *string|int|object*, set the way to pack/unpack server-side
    stored information about upload. This dataset is passed before upload to sync
    info about uploaded content during processing and changing during upload itself
    as progressing. With this directive you can set the format of stored data. The
    value must be either the child of
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders\AEncoder```
    class or class-string with class implementing that abstraction or known
    id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders\Factory```.
    Will be ignored when the target is set to somewhere remote.
* **data_modifier** - *string|int|object*, set the way to pack/unpack server-side
    stored information about upload. This directive is about making the data
    compact for storage without affecting the structure - it's possible to set
    different compact mode and data encoder on different instances depending on
    the needs. The value must be either the child of
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers\AModifier```
    class or class-string with class implementing that abstraction or known
    id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers\Factory```.    
    The example: data encoder make JSON and then data modifier put it into HEX string.
    Will be ignored when the target is set to somewhere remote.
* **key_encoder** - *string|int|object*, set the way to encode shared key from
    the server side. So you can choose what will be the key under which the
    upload will be known. The value must be either the child of
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders\AEncoder```
    class or class-string with class implementing that abstraction or known
    id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders\Factory```.    
    Will be ignored when the target is set to somewhere remote.
* **key_modifier** - *string|int|object*, set the way to pack shared key from
    the server side. So you can choose what will be the key under which the
    upload will be known. The value must be either the child of
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers\AModifier```
    class or class-string with class implementing that abstraction or known
    id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers\Factory```.    
    The example: key encoder uses full path and then key modifier put it into HEX string.
    Will be ignored when the target is set to somewhere remote.

    Value processed by *key_encoder* and *key_modifier* will be returned to the
    client and will be expected in next call to as key to pair known upload.
* **driving_file** - *string|int|object*, where to store file with data about
    processing. Usually it can be the same directory as defined in *temp_location*.
    But you can use different storage. The value must be either the instance of
    ```\kalanis\UploadPerPartes\Interfaces\IDrivingFile``` interface or
    class-string with class implementing that interface or name of storage
    or known id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage\Factory```.
    This is also the key for uploading without storing processing file.
* **temp_storage** - *string|object*, where to temporarily store the real
    file. Usually it is the same directory as defined in *temp_location*.
    But you can use different storage. The value must be either the instance of
    ```\kalanis\UploadPerPartes\Interfaces\ITemporaryStorage``` interface or
    some class with known storage background or string with name of storage
    or path to local storage.
* **temp_encoder** - *string|int|object*, how to encode key under which is
    it stored in the temporary storage. Usually file name. The value must
    be either the child of
    ```\kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders\AEncoder```
    class or class-string with class implementing that abstraction or name
    of encoder or known id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders\Factory```.
* **final_storage** - *string|int|object*, where to finally store the real
    file. Usually it is the same directory as defined in *target_location*.
    But you can use different storage. The value must be either the instance of
    ```\kalanis\UploadPerPartes\Interfaces\IFinalStorage``` interface or
    some class with known storage background or string with name of storage
    or path to local storage.
* **final_encoder** - *string|int|object*, how to encode key under which is
    it stored in the final storage. Usually the original file name. The value
    must be either the child of
    ```\kalanis\UploadPerPartes\Target\Local\TemporaryStorage\FinalStorage\AEncoder```
    class or class-string with class implementing that abstraction or name
    of encoder or known id of currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\TemporaryStorage\FinalStorage\Factory```.
* **checksum** - *string*, the way in which is checked the content of upload.
    Usually used after crashed upload. The value must be either the class-string
    implementing ```\kalanis\UploadPerPartes\Interfaces\IChecksum```
    interface or name of checksum method currently available instances as seen in
    ```\kalanis\UploadPerPartes\Target\Local\Checksums\Factory```.
* **decoder** - *string*, which method will be used for passing binary data
    through the wilderness of the network. Must be known by both client and server.
    Will be passed on init to the client and he will pass it back with each segment.
    The default is *base64*.
* **can_continue** - *bool*, if you can re-initialize upload and continue from
    previously stopped run.

With a special setting you can do anonymous share where you have just the server
with data storage and the all upload process is on your clients. And then the
other side must prove what the heck is inside that files before their action.

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
