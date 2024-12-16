# AsseticBundle v3.x

[![Latest Stable Version](https://poser.pugx.org/fabiang/assetic-module/version)](https://packagist.org/packages/fabiang/assetic-module)
[![License](https://poser.pugx.org/fabiang/assetic-module/license)](https://packagist.org/packages/fabiang/assetic-module)
[![CI](https://github.com/fabiang/assetic-module/actions/workflows/ci.yml/badge.svg)](https://github.com/fabiang/assetic-module/actions/workflows/ci.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fabiang/assetic-module/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/fabiang/assetic-module/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/fabiang/assetic-module/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/fabiang/assetic-module/?branch=develop)

Currently maintained fork of [widmogrod/zf2-assetic-module](https://github.com/widmogrod/zf2-assetic-module).
**Note:** Please consider using a different framework for managing your assets, e.g., Webpack.
While this is still maintained, this module is considered feature-complete and, therefore, only bug fixes will be accepted.

## Features

  * [x] Add support for PHP 8.0+
  * [x] Drop support for PHP <7.4
  * [x] Remove support for Zend Framework
  * [x] Use return-types and type-hints everywhere
  * [x] Support for Mezzio (sorry no Expressive)

## What is this?

Assets management per module made easy.
Every module can come with their own assets (JS, CSS, Images etc.) and this
module make sure the assets are moved into your public folder and are directly
available in your views.

This also helps you to load all assets for your Laminas application which you've
installed with npm, yarn etc.

  * **Optimize your assets**. Minify your css, js; Compile scss, and more...
  * **Adapts To Your Needs**. Using custom template engine and want to use power of this module, just implement `Fabiang\AsseticBundle\View\StrategyInterface`
  * **Well tested**. Besides unit test this solution is also ready for the production use.
  * **Great fundations**. Based on [Assetic](https://github.com/assetic/framework) and [Laminas](https://getlaminas.org)
  * **Excellent community**. Everything is thanks to great support from GitHub & PHP community!
  * **Every change is tracked**. Want to know whats new? Take a look at [CHANGELOG.md](https://github.com/fabiang/assetic-module/blob/main/CHANGELOG.md)
  * **Listen to your ideas**. Have a great idea? Bring your tested pull request or open a new issue.


## Installation

Read [the quick start guide for Laminas\Mvc](https://github.com/fabiang/assetic-module/blob/main/docs/howto-mvc.md)
or [the quick start guide for Mezzio?](https://github.com/fabiang/assetic-module/blob/main/docs/howto-mezzio.md)

## Documentation

  * [How to start with Laminas MVC?](https://github.com/fabiang/assetic-module/blob/main/docs/howto-mvc.md)
  * [How to start with Mezzio?](https://github.com/fabiang/assetic-module/blob/main/docs/howto-mezzio.md)
  * [Configuration](https://github.com/fabiang/assetic-module/blob/main/docs/config.md)
  * [Tips & Tricks](https://github.com/fabiang/assetic-module/blob/main/docs/tips.md)
  * [Migration guide](https://github.com/fabiang/assetic-module/blob/main/docs/migration.md)
