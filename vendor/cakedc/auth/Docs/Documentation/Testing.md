Testing
====

This plugins provides a way to easily check your permissions and roles configuration.

Setup
-----

Create a new test class and extends to `IntegrationTestCase`
```php
class PermissionsTest extends IntegrationTestCase
{
    use \CakeDC\Auth\Test\BaseTestTrait;
}
```

Now you need to define a `provider` method and it must return an array with the filenames. Note that each filename must be an array itself. We recommend to create one file per roles to keep things as clean as possible but it is up to you.

```php
/**
 * @return array
 */
public function provider()
{
    return [
        ['first.csv'],
        ['second.csv'],
        ['third.csv'],
    ];
}
```

Finally you only need to create CSV files in `tests/Provider` folder.

CSV file template
-----------------

Each line must include `url`, `username`, `method` (`GET` or `POST`), `Ajax?` (`ajax` or `no-ajax`), `Response Code Expected` (`200`, `302`, `404`, etc) and it may include `Response Contains` with anything present in response body.
```csv
######## URL,USERNAME,METHOD,AJAX?,RESPONSE CODE EXPECTED,RESPONSE CONTAINS
/admin/my-url,administrator,get,no-ajax,200,my-page-content
```
