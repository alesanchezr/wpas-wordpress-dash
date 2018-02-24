# GravityForms

## Accessing the global context as dynamic form fields

By activating the GravityForms configuration you are able to access any global context variable
from the dynamic fields inside the gravity forms by prepending "wpas_" to the variable name, for example:
```
var WPAS_APP = {
    "ajax_url": "\/wp-admin\/admin-ajax.php",
    "view": {
        "type": "page",
        "slug": "apply",
        "template": ""
    },
    "url": "https:\/\/academy-web-alesanchezr.c9users.io\/apply",
    "controller": "",
    "lang": "en"
} 
```

To have the language on your form you have to add "wpas_lang" or to have the view type you add "wpas_view.type"

## Adittional Settings
```php
        $gfManager = new WPASGravityForm([
            
            // if true you can add CSS classes to the submit form
            'submit-button-class' => true, 
            
            //if true it will prepare the inputs for boptstrap
            'bootstrap4-styles' => true, 
            
            //A new type of input, similar to the combobox but with a "button group" style
            'fields' => [
                ['type' => 'button-group', 'label' => 'Button Group'] //A type of file that behaves like bootstrap button-grup as radiobutton
            ]
        ]);
```