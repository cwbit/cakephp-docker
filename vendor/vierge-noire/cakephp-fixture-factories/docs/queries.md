<h1 align="center">Queries</h1>


The fixture factories are closely related to the database. The package provides several methods to conveniently
run queries. All methods will by-pass the `beforeFind` event, enabling the direct inspection of your
test database.

These methods are meant to help performing assertions in the "Assert" part of your tests. Do not use the `::find()` method in the "Act" part of your tests, e.g. to test finders.

## ArticleFactory::find()
This method will return a query on the table related to the given factory. It takes as input the same parameters as the classic table `find()` method.
More documentation on the `find` method [here](https://book.cakephp.org/4/en/orm/query-builder.html#namespace-Cake\ORM).

## ArticleFactory::count()
This method will return the number of entries in the table of the given factory.

## ArticleFactory::get()
This method will return an entity based on its primary key.
More documentation on the `get` method [here](https://book.cakephp.org/4/en/orm/retrieving-data-and-resultsets.html#getting-a-single-entity-by-primary-key).
