![](https://github.com/googleapis/google-api-php-client/workflows/.github/workflows/tests.yml/badge.svg)

# Upload files into Google Drive using Google OAuth - PHP API

<dl>
  <dt>Reference Docs</dt><dd><a href="https://googleapis.github.io/google-api-php-client/master/">https://googleapis.github.io/google-api-php-client/master/</a></dd>
  <dt>License</dt><dd>Apache 2.0</dd>
</dl>

The Google API Client Library enables you to work with Google APIs such as Gmail, Drive or YouTube on your server.

These client libraries are officially supported by Google.  However, the libraries are considered complete and are in maintenance mode. This means that we will address critical bugs and security issues but will not add any new features.

## Google Cloud Platform

For Google Cloud Platform APIs such as Datastore, Cloud Storage or Pub/Sub, we recommend using [GoogleCloudPlatform/google-cloud-php](https://github.com/googleapis/google-cloud-php) which is under active development.

## Requirements ##
* [PHP 5.4.0 or higher](https://www.php.net/)

## Developer Documentation ##

The [docs folder](docs/) provides detailed guides for using this library.

## Installation ##

You can use **Composer** or simply **Download the Release**

### Composer

The preferred method is via [composer](https://getcomposer.org/). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```sh
composer install
```

- You may have to add the credentials.json file into the root directory. 
- Create upload/ folder to store objects to be uploaded.

## Demo ##
* [Google Drive OAuth - LankaHot.net](https://auth.lankahot.net/gdriveauth/index.php)