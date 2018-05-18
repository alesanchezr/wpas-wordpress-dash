# WPASAdminNotifier

Send notifications to the admin user very easy

```php
//To notify an error, add this anywhere you want, on any hooks or class
WPAS\Messaging\WPASAdminNotifier::error('There has been an error');
WPAS\Messaging\WPASAdminNotifier::info('You plugin has been installed');
WPAS\Messaging\WPASAdminNotifier::warning('Your file as imported with errors');
WPAS\Messaging\WPASAdminNotifier::success('Happy world!!');

//Add this *** AT THE END *** of your functions PHP
WPAS\Messaging\WPASAdminNotifier::loadTransientMessages();
```

### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
