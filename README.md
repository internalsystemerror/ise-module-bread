# BREAD module for Zend Framework 2+

## Introduction
This module intends to offer BREAD (Browse, Read, Edit, Add, Delete) functionality for entities, as dubbed "ORM over HTTP" (thanks [alextech](https://github.com/alextech).

## Requirements

At present only a DoctrineORM mapper is provided. If you do not wish to implement your own mapper (for Zend\Db for example), then you will be required to include one of:
 - [DoctrineORMModule](https://github.com/doctrine/DoctrineORMModule) - Expected out of the box.

This module also works well with:
 - [Ise\Admin](https://github.com/internalsystemerror/ise-module-admin) - Provides user authentication / role based account control.
 - [Ise\Bootstrap](https://github.com/internalsystemerror/ise-module-bootstrap) - Integrates with Twitter Bootstrap.

## Installation

Installation of this module uses composer.
```sh
php composer.phar require ise/ise-module-bread
```

Then add the following modules into your Zend Framework configuration.
 - Ise\Bread

You will also need to ensure that you have installed any mapper dependencies (for example, install DoctrineORMModule to use DoctrineOrmMapper).

## Usage

#### Configuration

In order to provide BREAD functionality for entities, you need to add them to your module/application configuration. You can do this as shown in [ise_bread.global.php.dist](config/ise_bread.global.php.dist)

#### Controllers

TODO

#### Services

TODO

#### Mappers

