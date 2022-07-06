<h1 align="center">Bake command</h1>

We recommend you to use the bake command in order create your factories.


Make sure to add:
```php
$this->addPlugin('CakephpFixtureFactories');
```

in the `bootstrap` method of your `Application.php`. See here for more details on [how to load a Plugin](https://book.cakephp.org/4/en/plugins.html#loading-a-plugin).

The command
```css
bin/cake bake fixture_factory -h
```
will assist you. You have the possibility to bake factories for all (`-a`) your models. You may also include help methods (`-m`)
based on the associations defined in your models. Factories can be baked within plugin with the command `-p`.

