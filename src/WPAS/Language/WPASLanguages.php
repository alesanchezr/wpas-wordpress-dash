<?php

namespace WPAS\Language;

use WPAS\Utils\HelperStringFormat;
use WPAS\Utils\WPASException;

class WPASLanguages{
	
	public static $currentLanguage = null;
	private static $languagesDirectory = null;
	private static $isAdmin = true;
	
	function __construct($settings) {
		
		//self::$isAdmin = is_admin();
		self::$isAdmin = false;
		if(!self::$isAdmin)
		{
			if(!empty($settings['languages-directory'])) 
				self::$languagesDirectory = $settings['languages-directory'];
		
			$this->activate();	
		}
	}
	
	private function activate(){
		if (function_exists( 'pll_register_string' ))
		{
			if(empty(self::$languagesDirectory)) throw new WPASException('You need to specify a laguages directory');
			
			self::$currentLanguage = require(self::$languagesDirectory.'en.lang.php');
			if(!self::$currentLanguage) throw new WPASException("Unable to load language array from ".$languageURL, 1);

			if(empty(self::$currentLanguage['labels']))  throw new WPASException("The language array must have a 'labels' key with the list of key->value strings to register", 1);
			
			$languageArray = self::$currentLanguage['labels'];
			//print_r($languageArray); die();
			foreach ($languageArray as $key => $value) {
				pll_register_string($key, $value);
			}
			
		}else throw new WPASException('The class WPASLanguages requires the Polylang plugin');
	}
	
	public static function getActivityTemplate($key, $args){
		
		if(!self::$currentLanguage) throw new Exception("Could not find the languages file");
		
		$activities = self::$currentLanguage['activities'];
		if(!isset($activities[$key])) throw new Exception("Invalid activity template string key");
		
		return [
			"description" => HelperStringFormat::sprintf($activities[$key]['description'], $args),
			"title" => HelperStringFormat::sprintf($activities[$key]['title'], $args)
		];
	}
	
	public static function getStudentTemplate($key, $args){
		
		if(!self::$currentLanguage) throw new Exception("Could not find the languages file");
		
		$studentTemplate = self::$currentLanguage['student'];
		if(!isset($studentTemplate[$key])) throw new Exception("Invalid student template string key");
		
		//print_r($args); die();
		
		return HelperStringFormat::sprintf($studentTemplate[$key], $args);
	}
}