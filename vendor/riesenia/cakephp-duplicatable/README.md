# Duplicatable behavior for CakePHP

[![Build Status](https://img.shields.io/github/workflow/status/riesenia/cakephp-duplicatable/CI/master?style=flat-square)](https://github.com/riesenia/cakephp-duplicatable/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://img.shields.io/codecov/c/github/riesenia/cakephp-duplicatable.svg?style=flat-square)](https://codecov.io/github/riesenia/cakephp-duplicatable)
[![Latest Version](https://img.shields.io/packagist/v/riesenia/cakephp-duplicatable.svg?style=flat-square)](https://packagist.org/packages/riesenia/cakephp-duplicatable)
[![Total Downloads](https://img.shields.io/packagist/dt/riesenia/cakephp-duplicatable.svg?style=flat-square)](https://packagist.org/packages/riesenia/cakephp-duplicatable)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

This plugin contains a behavior that handles duplicating entities including related data.

## Installation

Using composer

```
composer require riesenia/cakephp-duplicatable
```

Load plugin using

```sh
bin/cake plugin load Duplicatable
```

## Usage

This behavior provides multiple methods for your `Table` objects.

### Method `duplicate`

This behavior provides a `duplicate` method for the table. It takes the primary key of the record to duplicate as its only argument.
Using this method will clone the record defined by the primary key provided as well as all related records as defined in the configuration.

### Method `duplicateEntity`

This behavior provides a `duplicateEntity` method for the table. It mainly acts as the `duplicate` method except it does not save the duplicated record but returns the Entity to be saved instead. This is useful if you need to manipulate the Entity before saving it.

## Configuration options:

* *finder* - finder to use to get entities. Set to "translations" to fetch and duplicate translations too. Defaults to "all". Possible to set an array for more finders.
* *contain* - set related entities that will be duplicated
* *remove* - fields that will be removed from the entity
* *set* - fields that will be set to provided value or callable to modify the value. If you provide a callable, it will take the entity being cloned as the only argument
* *prepend* - fields that will have value prepended by provided text
* *append* - fields that will have value appended by provided text
* *saveOptions* - options for save on primary table
* *preserveJoinData* - if `_joinData` property in `BelongsToMany` relations should be preserved - defaults to `false` due to tricky nature of this association

## Examples

```php
class InvoicesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // add Duplicatable behavior
        $this->addBehavior('Duplicatable.Duplicatable', [
            // table finder
            'finder' => 'all',
            // duplicate also items and their properties
            'contain' => ['InvoiceItems.InvoiceItemProperties'],
            // remove created field from both invoice and items
            'remove' => ['created', 'invoice_items.created'],
            // mark invoice as copied
            'set' => [
                'name' => function($entity) {
                    return md5($entity->name) . ' ' . $entity->name;
                },
                'copied' => true
            ],
            // prepend properties name
            'prepend' => ['invoice_items.invoice_items_properties.name' => 'NEW '],
            // append copy to the name
            'append' => ['name' => ' - copy']
        ]);

        // associations (InvoiceItems table hasMany InvoiceItemProperties)
        $this->hasMany('InvoiceItems', [
            'foreignKey' => 'invoice_id',
            'className' => 'InvoiceItems'
        ]);
    }
}

// ... somewhere in the controller
$this->Invoices->duplicate(4);
```
