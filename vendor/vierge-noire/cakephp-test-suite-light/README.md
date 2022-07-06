# cakephp-test-suite-light
A fast test suite for CakePHP applications

#### For CakePHP 3.x
composer require --dev vierge-noire/cakephp-test-suite-light "^1.0"

#### For CakePHP 4.x
composer require --dev vierge-noire/cakephp-test-suite-light "^2.0"

## Installation

### Listeners

For CakePHP ^4.3 application, no additional listener is required. See the doc [here](https://book.cakephp.org/4.next/en/appendices/fixture-upgrade.html#fixture-upgrade). 

Prior to CakePHP 4.3:

Make sure you *replace* the native CakePHP listener by the following one inside your `phpunit.xml` (or `phpunit.xml.dist`) config file, per default located in the root folder of your application:

```
<!-- Setup a listener for fixtures -->
     <listeners>
         <listener class="CakephpTestSuiteLight\FixtureInjector">
             <arguments>
                 <object class="CakephpTestSuiteLight\FixtureManager" />
             </arguments>
         </listener>
     </listeners>
``` 

Between each test, the package will truncate all the test tables that have been used during the previous test.

The fixtures will be created in the test database(s) defined in your [configuration](https://book.cakephp.org/4/en/development/testing.html#test-database-setup).

***Important: you should not add the [CakePHP native listener](https://book.cakephp.org/3/en/development/testing.html#phpunit-configuration)*** to your `phpunit.xml` file.
Only one listener is required, which is the one described in the section *Installation*.

### Truncating tables

#### With CakePHP ^4.3
Use the `CakephpTestSuiteLight\Fixture\TruncateDirtyTables` trait in a test case class
in order to clean up the database prior to each of its tests.

#### Prior to CakePHP ^4.3
The package will empty by default the dirty tables in all test databases.

If you with to ignore the truncation for a given test case, you may use the
`CakephpTestSuiteLight\SkipTablesTruncation` trait

If you wish to ignore a given connection, you may 
provide the `skipInTestSuiteLight` key to `true` in your `config/app.php`. E.g.:  

```$xslt
In config/app.php
<?php
...
'test_connection_to_be_skipped' => [
    'className' => Connection::class,
    'driver' => Mysql::class,
    'persistent' => false,
    ...
    'skipInTestSuiteLight' => true
],
```

This can be useful for example if you have connections to a third party server in the cloud that should be ignored.

## Test life cycle

Here is the only step performed by the Fixture Factories Fixture Manager, and how to disable it.

### Truncating tables

The Fixture Manager truncates the dirty tables at the beginning of each test. This is the only action performed.

Dirty tables are tables on which the primary key has been incremented at least one. The detection of dirty tables is made
with an SQL query by dedicated classes. These are called `TableSniffers` and are located in the `src/TestSuite/Sniffer` folder
 of the package. These are provided for:
* Sqlite
* MySQL
* Postgres

If you use a different database engine, you may provide your own. It should extend
the `BaseTableSniffer` class.

You should then map in your `config/app.php` file the driver to
the custom table sniffer for each relevant connection. E.g.:
```$xslt
In config/app.php
<?php
...
'test' => [
    'className' => Connection::class,
    'driver' => Mysql::class,
    'persistent' => false,
    ...
    'tableSniffer' => '\Your\Custom\Table\Sniffer'
],
```

### Temporary vs non-temporary dirty table collector

One of the advantage of the present test suite, consists in the fact that the test database is cleaned before each test,
rather than after. This enables the developer to perform queries in the test database and observe the state in which
a given test left the database.

The present plugin collects the dirty tables in a dedicated table with the help of triggers.
This table is per default permanent, but it can be set to temporary in order to keep it invisible to the code.

In ordert to do so, in your test DB settings, set the key `'dirtyTableCollectorMode'` to `'TEMP'`.

### Using CakePHP fixtures

It is still possible to use the native CakePHP fixtures. To this aim, you may simply load them as described [here](https://book.cakephp.org/3/en/development/testing.html#creating-fixtures).

### Statistic tool

The suite comes with a statistic tool. This will store the execution time, the test name, the number and the list
of the dirty tables for each test.

In order to activate it, add a second argument set to true to the `FixtureInjector` in the following manner:

```
<!-- Setup a listener for fixtures -->
     <listeners>
         <listener class="CakephpTestSuiteLight\FixtureInjector">
             <arguments>
                 <object class="CakephpTestSuiteLight\FixtureManager" />
                 <boolean>true</boolean>
             </arguments>
         </listener>
     </listeners>
```

The statistics will be store after each suite in `tmp/test_suite_light/test_suite_statistics.csv`.

With the help of your IDE, you can easily order the results and track the slow tests, and improve their respective performance.

Note that the statistic tool does not perform any query in the database. It uses information 
that is being gathered regardless of its actvation. It has no significant impact on the
overall speed of your tests. 

## Authors
* Juan Pablo Ramirez
* Nicolas Masson


## Support
Contact us at vierge.noire.info@gmail.com for professional assistance.

You like our work? [![ko-fi](https://www.ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/L3L52P9JA)


## License

The CakephpTestSuiteLight plugin is offered under an [MIT license](https://opensource.org/licenses/mit-license.php).

Copyright 2020 Juan Pablo Ramirez and Nicolas Masson

Licensed under The MIT License Redistributions of files must retain the above copyright notice.
