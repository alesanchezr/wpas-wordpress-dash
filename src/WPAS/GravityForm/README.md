# GravityForms

```php
        $gfManager = new WPASGravityForm([
            'submit-button-class' => true, // if true you can add CSS classes to the submit form
            'populate-current-language' => true, //if true it will populate form every dynamic fiel "wpas_language" with current polylang language
            'fields' => [
                ['type' => 'button-group', 'label' => 'Button Group'] //A type of file that behaves like bootstrap button-grup as radiobutton
            ]
        ]);
```