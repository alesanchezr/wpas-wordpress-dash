<?php

namespace WPAS\Language;

use WPAS\Utils\HelperStringFormat;
use WPAS\Utils\WPASException;

require_once('global_functions.php');

class WPASLanguages{
	
	public static $currentTranslations = null;
	public static $currentLanguage = null;
	private static $languagesDirectory = null;
	private static $isAdmin = true;
	
	function __construct($settings) {
		
		self::$isAdmin = is_admin();
		
		if(!empty($settings['languages-directory'])) 
			self::$languagesDirectory = $settings['languages-directory'];
			
		if(empty(self::$languagesDirectory)) throw new WPASException('You need to specify a laguages directory');
		
		self::$currentLanguage = pll_current_language();
		
		add_filter('wpas_fill_content', function($data){
			$data['lang'] = self::$currentLanguage;
			return $data;
		},10,1);
		
		//$languageUrl = self::$languagesDirectory.self::$currentLanguage.'.lang.php';
		$languageUrl = self::$languagesDirectory.'all.lang.php';
		self::$currentTranslations = require($languageUrl);
		if(!self::$currentTranslations) throw new WPASException("Unable to load language array from ".$languageUrl, 1);
		
		if(self::$isAdmin)
		{
			add_action('current_screen', [$this, 'load_translations']);
		}
	}
	
	public function load_translations(){
		
		$screen = get_current_screen();
		if($screen->id != 'languages_page_mlang_strings') return;
		
		if (function_exists( 'pll_register_string' ))
		{
			if(empty(self::$currentTranslations['labels']))  throw new WPASException("The language array must have a 'labels' key with the list of key->value strings to register", 1);
			
			$languageArray = self::$currentTranslations['labels'];
			foreach ($languageArray as $key => $value) {
				pll_register_string($key, $value);
			}
			
		}else throw new WPASException('The class WPASLanguages requires the Polylang plugin');
	}
	
	public static function getSlug($key){
		if(is_admin()) return $key;
		if(!self::$currentTranslations) throw new WPASException("Could not find the languages file");
		$slugs = self::$currentTranslations['slugs'];

		if(empty($slugs)) throw new WPASException("No slugs have been defined on the translations");
		if(empty($slugs[$key])) throw new WPASException("The slug $key has not been defined in the translation file");
		if(empty($slugs[$key][self::$currentLanguage])) throw new WPASException("The slug $key has no value for language ".self::$currentLanguage);
		
		return $slugs[$key][self::$currentLanguage];
	}
	
	public static function getStudentTemplate($key, $args){
		
		if(!self::$currentTranslations) throw new WPASException("Could not find the languages file");
		
		$studentTemplate = self::$currentTranslations['student'];
		if(!isset($studentTemplate[$key])) throw new WPASException("Invalid student template string key");
		
		return HelperStringFormat::sprintf($studentTemplate[$key], $args);
	}
}
