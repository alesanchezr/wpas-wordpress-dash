# WordPress Dash command line tool (CLI)

You can use command line to generate the any code you need for the Controllers and CustomPostTypes.

## Instalation

In order to be able to use the commands, please run this command after downloaded the library with composer

```sh
$ php vendor/alesanchezr/wpas-wordpress-dash/run.php <your_theme_directory_name>
```

That command is going to create your theme with the basic structure necesary to start coding.

## Other available commands

Right now, there are only two other commands available:

1) Generate a new controller file.
```sh
$ wp dash-generate <YourController>
```
Note: Your controller names must end with "Controller", for example: Car**Controller**


2) Generate a new custom post type file
```sh
$ wp dash-generate <YourPostType>
```
Note: Your PostType names must end with "PostType", for example: Course**PostType**

The tool will generate the folders and also the files

