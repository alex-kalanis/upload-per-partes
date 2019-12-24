# Upload Per-Partes

Uploading files via HTTP style per-partes

Contains libraries for uploading large files with JavaScript FileApi
with things like real truth-telling progress bar or available upload resume.

This is the mixed package - contains sever-side implementation in Python and PHP.

# PHP Installation

```
{
    "require": {
        "alex-kalanis/upload-per-partes": "dev-master"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


# PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Connect the "UploadPerPartes\Upload" into your app. When it came necessary
you can extends every library to comply your use-case; mainly your storage and
processing.

3.) Copy and connect the frontend library "uploader.ts" into your app. You need
something like Grunt to translate TypeScript into JavaScript. You can also extends
included classes to comply your use-case; mainly for styling.

# Python Installation

into your "setup.py":

```
    install_requires=[
        'kw_upload',
    ]
```

# Python Usage

1.) Connect the "kw_upload\upload" into your app. When it came necessary
you can extends every library to comply your use-case; mainly your storage and
processing.

3.) Copy and connect the frontend library "uploader.ts" into your app. You need
something like Grunt to translate TypeScript into JavaScript. You can also extends
included classes to comply your use-case; mainly for styling.