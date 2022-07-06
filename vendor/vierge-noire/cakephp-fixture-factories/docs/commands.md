<h1 align="center">Persist command</h1>


Factories can also conveniently populate your database in order to test your application on the browser.
The following command will persist 5 articles, each with 3 irish authors, considering that the `ArticleFactory` class features
a `withThreeIrishAuthors()` method:
```css
bin/cake fixture_factories_persist Authors -n 5 -m withThreeIrishAuthors
```
The option `--dry-run` or `-d` will display the output without persisting.
The option `-c` will persist in the connection provided (default is `test`).
The option `-w` will create associated fixtures.

The `fixture_factories_persist` command is featured on CakePHP 4 only (open to contribution for CakePHP 3).
