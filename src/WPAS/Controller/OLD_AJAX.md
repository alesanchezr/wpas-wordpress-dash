Note: this way or working is deprecated, use the [API Controller instead]().

## Working with AJAX

1. Start by specifiying every AJAX request you will do
```php
//Using a 'General' controller class to process the 'newsletter_signup' ajax action in 'all' views
$controller->routeAjax([ 'slug' => 'all', 'controller' => 'General:newsletter_signup' ]);  
```

1. Do your ajax call inside any of your enqueued javascript files
```js
        var requestData = { 
            action: 'signup',
            //any other params you want to send in the request
            foo: var
        };

        $.ajax({
            action: 'post',
            url: WPAS_APP.ajax_url,
            data: requestData,
            result: function(responseData){
                console.log(responseData);
            }
        });
```
Note: The WPAS_APP object will be available anywhere on your JS files, it contains information about your website like language (if multilang), current slug, etc.

## Logging

Set WP_DEBUG_LOG = true to start logging, check the /logs directory in your wordpress root.

```php
//as a part of your wp-config.php
define('WP_DEBUG_LOG', true);
```

## Options

When intanciating a new WPASController you can to specify the following options:

| Option                            | Default   | Description  |
|-----------------------------------|-----------|----------------------------------------------------------|
| namespace (required)              | ''        | PHP namespace in which all your controller classes are goign to be declared |
| data (optional)                   | []        | any data you want to append to the data array |
| mainscript-requierments (optinal) | ''        | ['script1',] |
| namespace (optional)              | ''        | PHP namespace in which all your controller classes are goign to be declared |
