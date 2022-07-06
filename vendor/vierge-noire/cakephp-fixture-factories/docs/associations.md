<h1 align="center">Associations</h1>

If your application is not using CakePHP, or if you want to add
associations within your factories, please take a look at the [Associations for non-CakePHP apps](./no_cake_associations.md) section
in order to define the associations of your tables. After defining your associations, you may
continue with the documentation below.

## The _with_ method
If you have baked your factories with the option `-m` or `--methods`, you will have noticed that a method for each association
has been inserted in the factories. This will assist you creating fixtures for the associated models. For example, we can
create an article with 10 authors as follows:
```php
 $article = ArticleFactory::make()->with('Authors', AuthorFactory::make(10))->persist();
```
or using the method defined in our `ArticleFactory`:
```php
$article = ArticleFactory::make()->withAuthors(10)->persist();
```

If we wish to randomly populate the field `biography` of the 10 authors of our article, with 10 different biographies:
```php
$article = ArticleFactory::make()->withAuthors(function(AuthorFactory $factory, Generator $faker) {
    return [
        'biography' => $faker->realText()
    ];
}, 10)->persist();
```
It is also possible to use the _dot_ notation to create associated fixtures:
```php
$article = ArticleFactory::make()->with('Authors.Address.City.Country', ['name' => 'Kenya'])->persist();
```
will create an article, with an author having itself an address in Kenya.

The second parameter of the method with can be:
* an array of field and their values
* an integer: the number
* a factory

Ultimately, the square bracket notation provides a mean to specify the number of associated
data created:
```php
$article = ArticleFactory::make(5)->with('Authors[3].Address.City.Country', ['name' => 'Kenya'])->persist();
```
will create 5 articles, having themselves each 3 different associated authors, all located in Kenya.

It is also possible to specify the fields of a toMany associated model.
For example, if we wish to create a random country with two cities having known names:

```php
$country = CountryFactory::make()->with('Cities', [
    ['name' => 'Nairobi'],
    ['name' => 'Mombasa'],
])->persist();
```

This can be useful if your business logic uses hard coded values, or constants.

Note that when an association has the same name as a virtual field,
the virtual field will overwrite the data prepared by the associated factory.

## Factory injection

When building associations, you may simply provide a factory as parameter. Example:

```php
$country = CountryFactory::make()->with('Cities',
  CityFactory::make()->threeCitiesAndFiveVillages()
)->persist();
```
will provide a country associated with three cities and five villages.

## Entity injection

You may also inject an exiting entity. The previous example would be now:
```php
$threeCitiesAndFiveVillages = CityFactory::make()->threeCitiesAndFiveVillages()->getEntities();
$country = CountryFactory::make()->with('Cities', $threeCitiesAndFiveVillages)->persist();
```

You may also pass an array of factories:
```php
$threeCitiesAndFiveVillages = CityFactory::make()->threeCitiesAndFiveVillages()->getEntities();
$country = CountryFactory::make()->with('Cities', [
    CityFactory::make()->threeCitiesAndFiveVillages(),
    CityFactory::make()->capitalCity()
])->persist();
```
