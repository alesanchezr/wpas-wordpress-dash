# WPASAdminNotifier

Send notifications to the admin user very easy

```php
//To notify an error, add this anywhere you want, on any hooks or class
WPASAdminNotifier::addTransientMessage(Utils\BCNotification::ERROR,'There has been an error');

//Add this at the end of your functions PHP
WPASAdminNotifier::loadTransientMessages();
```

### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
