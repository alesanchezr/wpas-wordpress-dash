
# Performance

This library WPASAsyncLoader requires you to use a manifest.json on your webpack configuration.

Here is how you can use the Performance Module to load your styles:

```php
$asyncLoader = new WPASAsyncLoader([
    'internal-url' => get_stylesheet_directory().'/public/',
    'public-url' => get_stylesheet_directory_uri().'/public/',
    'version' => 1,
    'debug' => true,
    'force-jquery' => true, //leaves jquery on the website
    'minify-html' => false,
    'styles' => [
        "category" => [ 
          "all" => 'main.css'
        ],
        "page" => [ 
          "all" => 'main.css',
          'gallery'=> ['main.css', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.10/css/lightgallery.min.css'],
        ],
        "custom-post" => [ 
          'venue'=> ['main.css', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.10/css/lightgallery.min.css'],
          "all" => 'main.css', 
        ]
    ],
    'scripts' => [
            "page" => [ "all" => ['main.js'] ],
            "custom-post" => [ "all" => ['main.js'] ]
        ]
    ]);
```

1. Load SVG inline cached SVG icons

```php
<?php wpas_get_inline_svg('assets/icons/inline','logicalthinking.svg'); ?>    
```

##Logging

Set WP_DEBUG_LOG = true to start logging, check the /logs directory in your wordpress root.

```php
//as a part of your wp-config.php
define('WP_DEBUG_LOG', true);
```


### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
