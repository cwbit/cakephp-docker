# CakePHP Authorization

![Build Status](https://github.com/cakephp/authorization/actions/workflows/ci.yml/badge.svg?branch=master)
[![Latest Stable Version](https://img.shields.io/github/v/release/cakephp/authorization?sort=semver&style=flat-square)](https://packagist.org/packages/cakephp/authorization)
[![Total Downloads](https://img.shields.io/packagist/dt/cakephp/authorization?style=flat-square)](https://packagist.org/packages/cakephp/authorization/stats)
[![Coverage Status](https://img.shields.io/codecov/c/github/cakephp/authorization.svg?style=flat-square)](https://codecov.io/github/cakephp/authorization)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Authorization stack for the CakePHP framework.

## Authorization not Authentication

This plugin intends to provide a framework around authorization and access
control. Authentication is a [separate
concern](https://en.wikipedia.org/wiki/Separation_of_concerns) that has been
packaged into a separate [authentication plugin](https://github.com/cakephp/authentication).

## Installation

You can install this plugin into your CakePHP application using
[composer](https://getcomposer.org):

```
php composer.phar require cakephp/authorization
```

Load the plugin by adding the following statement in your project's
`src/Application.php`:
```php
$this->addPlugin('Authorization');
```
or running the console command
```
bin/cake plugin load Authorization
```

## Documentation

Documentation for this plugin can be found in the [CakePHP
Cookbook](https://book.cakephp.org/authorization/2/en/)
