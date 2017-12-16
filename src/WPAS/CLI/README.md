# WordPress Dash command line tool (CLI)

You can use command line to generate the any code you need for the Controllers and CustomPostTypes.

## Instalation

In order to be able to use the commands, pelase add this code anywhere in your functions.php

```php
if ( class_exists( 'WP_CLI' ) ) { $i = new \WPAS\CLI\CLIManager(); }
```

## Available commands

Right now, there are only two commands available:

1) Generate a new controller file.
```sh
$ wp dash-generate <YourController>
```
Note: Your controller names must end in "Controller", for example: CarController


2) Generate a new custom post type file
```sh
$ wp dash-generate <YourPostType>
```
Note: Your PostType names must end in "PostType", for example: CoursePostType

The tool will generate the folders and also the files

