# AssetMix plugin for CakePHP

[![Latest Stable Version](https://poser.pugx.org/ishanvyas22/asset-mix/v/stable)](https://packagist.org/packages/ishanvyas22/asset-mix)
[![Total Downloads](https://poser.pugx.org/ishanvyas22/asset-mix/downloads)](https://packagist.org/packages/ishanvyas22/asset-mix)
[![License](https://poser.pugx.org/ishanvyas22/asset-mix/license)](https://packagist.org/packages/ishanvyas22/asset-mix)
[![CakePHP](https://img.shields.io/badge/cakephp-%5E4.0.0-red?logo=cakephp)](https://book.cakephp.org/4/en/index.html)
![Tests](https://github.com/ishanvyas22/asset-mix/workflows/Run%20tests/badge.svg?branch=master)
![PHPStan Check](https://github.com/ishanvyas22/asset-mix/workflows/Run%20PHPStan/badge.svg?branch=master)
![Coding Style Check](https://github.com/ishanvyas22/asset-mix/workflows/Check%20Coding%20Style/badge.svg?branch=master)

Provides integration with your [CakePHP application](https://cakephp.org/) & [Laravel Mix](https://laravel-mix.com).

This branch works with **CakePHP 4.0+**, see [version map](#version-map) for more details.

## ❤️  Support The Development
**Do you like this project? Support it by donating:**

<a href="https://www.buymeacoffee.com/ishanvyas" target="_blank">
    <img src="https://www.buymeacoffee.com/assets/img/custom_images/purple_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" >
</a>

<a href="https://www.patreon.com/ishanvyas">
    <img src="https://c5.patreon.com/external/logo/become_a_patron_button@2x.png" width="160">
</a>

**or** [Paypal me](https://paypal.me/IshanVyas?locale.x=en_GB)

**or** [![Contact me on Codementor](https://www.codementor.io/m-badges/isvyas/get-help.svg)](https://www.codementor.io/@isvyas?refer=badge)

### Follow me
- [GitHub](https://github.com/ishanvyas22)
- [Instagram](https://www.instagram.com/ishancodes)
- [LinkedIn](https://www.linkedin.com/in/ishan-vyas-314111112)
- [Twitter](https://twitter.com/ishanvyas22)

## Installation

1. Install the AssetMix plugin with composer:

    Via [composer](https://packagist.org/packages/ishanvyas22/asset-mix):
    ```bash
    composer require ishanvyas22/asset-mix
    ```
2. Load plugin using below command:
    ```bash
    bin/cake plugin load AssetMix
    ```
3. [Generate basic Javascript, CSS & Sass scaffolding](#generate-command):
    ```bash
    bin/cake asset_mix generate
    ```
    **Note:** Above command will generate scaffolding for vue, but you can generate [Bootstrap/jQuery](#generate-basic-bootstrapjquery-scaffolding), [React](#generate-react-scaffolding) or [Inertia](#generate-scaffolding-for-inertiajs) scaffolding too.
4. Install frontend dependencies
    - Using [npm](https://www.npmjs.com/):
    ```bash
    npm install
    ```
    or
    - Using [yarn](https://yarnpkg.com/):
    ```bash
    yarn install
    ```
5. [Compile your assets](https://laravel-mix.com/docs/4.0/workflow#step-4-compilation)
    - For development:
    ```bash
    npm run dev
    ```
    - To watch changes:
    ```bash
    npm run watch
    ```

    - For production:
    ```bash
    npm run prod
    ```
6. Load `AssetMix` helper from the plugin into your `AppView.php` file:
    ```php
    $this->loadHelper('AssetMix.AssetMix');
    ```

## Usage

After compiling your assets(js, css) with laravel mix, it creates a `mix-manifest.json` file into your `webroot` directory which contains information to map the files.

- To generate script tag for compiled javascript file(s):

```php
echo $this->AssetMix->script('app');
```

Above code will render:

```html
<script src="/js/app.js" defer="defer"></script>
```

As you can see it works same as [HtmlHelper](https://book.cakephp.org/3.0/en/views/helpers/html.html#linking-to-javascript-files). There is not need to provide full path or even file extension.

- To generate style tag for compiled css file(s):

```php
echo $this->AssetMix->css('main');
```

Output:

```html
<link rel="stylesheet" href="/css/main.css">
```

If [versioning](https://laravel-mix.com/docs/4.0/versioning) is enabled, output will look something like below:

```html
<link rel="stylesheet" href="/css/main.css?id=9c4259d5465e35535a2a">
```

## Generate command

The generate command is used to generate starter code for your Javascript application to get you started developing your frontend.

Get help:

```bash
bin/cake asset_mix -h
```

Generate default scaffolding (with vue):

```bash
bin/cake asset_mix generate
```

Above command will generate:
- `package.json`
- `webpack.mix.js`
- `assets/`
    - `css/`
    - `js/`
    - `sass/`

`assets/` directory is where you will store your js, css files which will compile down into your respective `webroot/` directory.

Custom directory name:

```bash
bin/cake asset_mix generate --dir=resources
```

You can also use custom directory name instead of default `assets` directory, above command will create `resources` directory where you can put your js, css, etc asset files.

Don't want to use Vue.js? Don't worry this plugin doesn't dictate on which Javascript library you should use. This plugin provides ability to quickly generate scaffolding for Vue as well as Bootstrap, and React.

#### Generate basic Bootstrap/jQuery scaffolding:

```bash
bin/cake asset_mix generate bootstrap
```

#### Generate React scaffolding:

```bash
bin/cake asset_mix generate react
```

#### Generate scaffolding for [Inertia.js](https://inertiajs.com/):

```bash
# for vue
bin/cake asset_mix generate inertia-vue

# or for react
bin/cake asset_mix generate inertia-react
```

#### Generate React scaffolding inside `resources` directory:

```bash
bin/cake asset_mix generate react --dir=resources
```

## Version map

AssetMix version | Branch | CakePHP version | PHP minimum version |
--- | --- | --- | --- |
1.x | master | >=4.0.0 | >=7.2 |
0.x | cake3 | >=3.5.0 | >=5.6 |

## Changelog
Please see [CHANGELOG](CHANGELOG-1.x.md) for more information about recent changes.

## Reference
To see this plugin into action you can refer to this [project](https://github.com/ishanvyas22/cakephpvue-spa), which will provide more insight.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
