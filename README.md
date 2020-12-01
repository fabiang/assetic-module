# AsseticBundle v3.0

[![Build Status](https://travis-ci.org/fabiang/assetic-module.png?branch=master)](https://travis-ci.org/fabiang/assetic-module)

Currently maintained fork of [widmogrod/zf2-assetic-module](https://github.com/widmogrod/zf2-assetic-module).

Goals for v3.0:

  * [x] Drop support for PHP <7.4
  * [ ] Add support for PHP 8.0
  * [x] Remove support for Zend Framework
  * [x] Use return-types and type-hints everywhere
  * [ ] Reach at least 70% unit test coverage
  * [x] Support for Mezzio (no Expressive)

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
  * **Every change is tracked**. Want to know whats new? Take a look at [CHANGELOG.md](https://github.com/fabiang/assetic-module/blob/master/CHANGELOG.md)
  * **Listen to your ideas**. Have a great idea? Bring your tested pull request or open a new issue.

## Installation

Read [the quick start guide for Laminas\Mvc](https://github.com/fabiang/assetic-module/blob/master/docs/howto-mvc.md)
or [the quick start guide for Mezzio?](https://github.com/fabiang/assetic-module/blob/master/docs/howto-mezzio.md)

## Documentation

  * [How to start with Laminas MVC?](https://github.com/fabiang/assetic-module/blob/master/docs/howto-mvc.md)
  * [How to start with Mezzio?](https://github.com/fabiang/assetic-module/blob/master/docs/howto-mezzio.md)
  * [Configuration](https://github.com/fabiang/assetic-module/blob/master/docs/config.md)
  * [Tips & Tricks](https://github.com/fabiang/assetic-module/blob/master/docs/tips.md)
  * [Migration guide](https://github.com/fabiang/assetic-module/blob/master/docs/migration.md)

## Developing

We've two main branches here:

- 2.x: version compatible with Zend Framework 2/3 and Laminas
- 3.x: future version with dropped Zend Framework support and added features

Currently the master branch is for version 2.x
