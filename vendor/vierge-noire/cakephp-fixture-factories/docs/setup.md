<h1 align="center">Setup</h1>

## Non-CakePHP apps
For non-CakePHP applications, you may use the method proposed by your framework
to manage the test database, or opt for the universal
[test database cleaner](https://github.com/vierge-noire/test-database-cleaner).

You should define your DB connections in your test `bootstrap.php` file as described
in the [cookbook](https://book.cakephp.org/4/en/orm/database-basics.html#configuration).

## CakePHP apps

To be able to bake your factories,
load the CakephpFixtureFactories plugin in your `src/Application.php` file:
```php
protected function bootstrapCli(): void
{
    // Load more plugins here
    $this->addPlugin('CakephpFixtureFactories');
}
```

**We recommend using migrations for managing the schema of your test DB with the [CakePHP Migrator tool.](https://book.cakephp.org/migrations/2/en/index.html#using-migrations-for-tests)**


## CakePHP 3 or < 4.3
For CakePHP anterior to 4.3 applications, you will need to use the [CakePHP test suite light plugin](https://github.com/vierge-noire/cakephp-test-suite-light#cakephp-test-suite-light)
to clean-up the test database prior to each test.

Make sure you **replace** the native CakePHP listener by the following one inside your `phpunit.xml` (or `phpunit.xml.dist`) config file,
per default located in the root folder of your application:

```xml
<!-- Setup a listener for fixtures -->
     <listeners>
         <listener class="CakephpTestSuiteLight\FixtureInjector">
             <arguments>
                 <object class="CakephpTestSuiteLight\FixtureManager" />
             </arguments>
         </listener>
     </listeners>
``` 

The following command will do that for you.

```css
bin/cake fixture_factories_setup
```

You can specify a plugin (`-p`) and a specific file (`-f`), if different from `phpunit.xml.dist`.

Between each test, the package will truncate all the test tables that have been used during the previous test.

**We recommend using migrations for maintaining your test DB with the [Migrator tool.](https://github.com/vierge-noire/cakephp-test-migrator)**


