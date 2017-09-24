# Language translations for WordPress made easy

//Returns the slug of any page in the current language
```php

<?php $slug = wpas_pll_get_slug('home'); ?>
<a href="<?php echo get_permalink( get_page_by_path( $slug ) ); ?>"> Click to go home </a>
```
