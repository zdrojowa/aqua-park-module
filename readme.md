# AquaParkModule

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

AquaPark list.

## Installation

Via Composer

``` bash
$ composer require zdrojowa/aqua-park-module
```

## NPM required:

``` bash
"axios": "^0.19",
"bootstrap-vue": "2.16.0"
"vue": "^2.6.10",
"vue-multiselect": "2.1.6",
"vuedraggable": "2.23.2",
```

## Usage

- Add in webpack.mix.js

``` bash
mix.module('AquaParkModule', 'vendor/zdrojowa/aqua-park-module');
```

- Add module AquaParkModule in config/selene.php

``` bash
'modules' => [
    AquaParkModule::class,
],

'menu-order' => [
    'AquaParkModule',
],
```

- run npm

``` bash
npm install
npm run prod
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/zdrojowa/aqua-park-module.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/zdrojowa/aqua-park-module.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/zdrojowa/aqua-park-module
[link-downloads]: https://packagist.org/packages/zdrojowa/aqua-park-module
[link-author]: https://github.com/zdrojowa
[link-contributors]: ../../contributors
