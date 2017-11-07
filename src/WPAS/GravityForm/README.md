# GravityForms

```php
        $gfManager = new WPASGravityForm([
            
            
            // if true you can add CSS classes to the submit form
            'submit-button-class' => true, 
            
            
            
            //if true it will populate form every dynamic fiel "wpas_language" with current polylang language
            'populate-current-language' => true, 
            
            
            
            //if true it will prepare the inputs for boptstrap
            'bootstrap4-styles' => true, 
            
            
            
            //A new type of input, similar to the combobox but with a "button group" style
            'fields' => [
                ['type' => 'button-group', 'label' => 'Button Group'] //A type of file that behaves like bootstrap button-grup as radiobutton
            ]
        ]);
```