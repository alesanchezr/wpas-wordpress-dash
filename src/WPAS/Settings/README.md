# WP Theme Settings

## Installation

Copy these two files into your theme
```sh
	wpas-themesettingsbuilder.css goes to: theme-folder/assets/css/
	wpas-themesettingsbuilder.js goes to: theme-folder/assets/js/
```

## Creating the page settings

First you you have to create a new instance of WPASThemeSettingsBuilder
and pass the settings that you want.

```php
    $settings = new WPASThemeSettingsBuilder([
        'general' => [
			'description' => 'Poker Society Options',
			'menu_slug' => 'ps_theme_options',
			'menu_title' => 'Theme Settings'
        ],
		'settingsID' => 'wp_theme_settings',
		'settingFields' => array('wp_theme_settings_title'), 
		'tabs' => [
			'general' => ['text' => 'General', 'dashicon' => 'dashicons-admin-page', 'tabFields' => $tabFields]
		]
    ]);
```

Each tab can contain several fields, you have to specify the tab content using
an array like this one:

```php
	$tabFields = [
			[ // In this example we have a select field to enable/disable mantainance mode
			    'type' => 'select', 
			    'label' => 'Mantainance Mode',
			    'options' => [
			    	'active' => 'Active',
			    	'innactive' => 'Innactive'
			    ],
			    'text' => 'Active',
			    'name' => 'mantainance-mode',
				'description' => 'Will block the website and display a mantainance mode screen'
			]
		];
```

## Retrieven settings options

Anywhere in your code you will now be able to retrieve any settings using the get_option option:
```php
$mantainance => get_option( 'mantainance-mode' )
```

## Preloading select fiels with dynamic data

You have to use the filter wpts_tab_[tab_key]_before to prepare the fields:

```php
add_filter('wpts_tab_activecampaign_before',array($this,'render_mytab_tab'));
//you receive the entire tab configuration
function render_mytab_tab($tab){
	
	$newfields =	[
						[
							'name' => 'activecampaign-utm-url-field',
							'options' => $auxFields
						],
					];
	
	foreach($newfields as $newfield)
	{
		for($i=0; $i<count($tab['tabFields']); $i++)
		{
			if($tab['tabFields'][$i]['name']==$newfield['name'])
			{
				foreach($newfield as $key => $value){
					$tab['tabFields'][$i][$key] = $value;
				}
			}
		}
	}
	
	return $tab;
}