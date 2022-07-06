<p align="center">
    <a href="https://vierge-noire.github.io/" target="_blank"><img src="https://vierge-noire.github.io/images/fixture_factories.svg" alt="ff-logo" width="150"  /></a>
</p>
<h1 align="center">
CakePHP Fixture Factories
</h1>
<h3 align="center">
Write and run your tests faster. On any PHP application.
</h3>

---

```php
ArticleFactory::make(5)->with('Authors[3].Address.City.Country')->persist();
```

---

## Installation
CakePHP 4 and non-CakePHP applications 

```
composer require --dev vierge-noire/cakephp-fixture-factories "^2.5"
```

CakePHP 3
```
composer require --dev vierge-noire/cakephp-fixture-factories "^1.0"
```
PHP 7.0 is supported up to v1.1.* only.

---

## Content

* ### [Setup - DB Cleaning](docs/setup.md)
* ### [Fixture Factories](docs/factories.md)
* ### [Test Fixtures](docs/examples.md)
* ### [Associations](docs/associations.md)
* ### [Associations for non-CakePHP apps](docs/no_cake_associations.md)
* ### [Scenarios](docs/scenarios.md)
* ### [Queries](docs/queries.md)
* ### [Bake command](docs/bake.md)
* ### [Persist command](docs/commands.md)

---


## Resources

[CakeFest 2021](https://www.youtube.com/watch?v=1WrWH2F_hWE) -
[IPC-Berlin 2020](https://www.youtube.com/watch?v=yJ6EqAE2NEs) - 
[CakeFest 2020](https://www.youtube.com/watch?v=PNA1Ck2-nVc&t=30s)

## Contribute

The development branch is named `next` (CakePHP 4.x based). Feel free to send us your pull requests!

## Support
Contact us at vierge.noire.info@gmail.com for professional assistance.

You like our work? [![ko-fi](https://www.ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/L3L52P9JA)

## Authors
Juan Pablo Ramirez and Nicolas Masson

## License

The CakePHPFixtureFactories plugin is offered under an [MIT license](https://opensource.org/licenses/mit-license.php).

Copyright 2021 Juan Pablo Ramirez and Nicolas Masson

Licensed under The MIT License Redistributions of files must retain the above copyright notice.
