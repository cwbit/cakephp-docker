<h1 align="center">Associations for non-CakePHP apps</h1>

Associations can be defined within the factories in the `initialize()` method.
The `getTable()` method provides public access to the model class used by the factories. If not defined in your application
(which is probably the case if your app in not built with CakePHP), the model class is generated automatically, based on the table name returned by the
`getRootTableRegistryName` method.

For example in the cities' table factory, you may define the association of the `cities` belonging
to a `country` and having many addresses as follows:

```php
// In App\Test\Factory\CityFactory

protected function getRootTableRegistryName(): string
{
    return "Cities";
}

protected function initialize(): void
{
    $this->getTable()
        ->belongsTo('Country')
        ->hasMany('Addresses');
}
```

Once this is defined, you may then call:
```php
$city = CityFactory::make()
    ->with('Addresses', 4)
    ->with('Country', ['name' => 'India'])
    ->getEntity();
```
which will set the country where the city is created, and provide 4 random addresses.

You will find described in the cookbook [HERE](https://book.cakephp.org/4/en/orm/associations.html) how to define your associations.
Non CakePHP applications will not need to create any table objects, but rather use the `getTable()` public method.
