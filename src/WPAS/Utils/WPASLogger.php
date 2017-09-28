<?php
namespace WPAS\Utils;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class WPASLogger {

	protected static $instances = [];
	protected static $currentIndex;

	/**
	 * Method to return the Monolog instance
	 *
	 * @return \Monolog\Logger
	 */
	static public function getLogger($class=null)
	{
		if (!isset(self::$instances[$class])) {
			self::configureInstance($class);
		}

		return self::$instances[self::$currentIndex];
	}

	/**
	 * Configure Monolog to use a rotating files system.
	 *
	 * @return Logger
	 */
	protected static function configureInstance($class)
	{
		$dir = ABSPATH . 'logs';

		if (!file_exists($dir)){
			mkdir($dir, 0777, true);
		}
		
		self::$currentIndex = $class;
		if(!isset(self::$instances[$class])) self::$instances[$class] = new Logger($class);
		//echo $dir . DIRECTORY_SEPARATOR .'wordpress.log'; die();
		self::$instances[$class]->pushHandler(new StreamHandler($dir . DIRECTORY_SEPARATOR .'wordpress.log', Logger::DEBUG));
	}

	public static function debug($message, array $context = []){
		self::getLogger()->addDebug($message, $context);
	}

	public static function info($message, array $context = []){
		self::getLogger()->addInfo($message, $context);
	}

	public static function notice($message, array $context = []){
		self::getLogger()->addNotice($message, $context);
	}

	public static function warning($message, array $context = []){
		self::getLogger()->addWarning($message, $context);
	}

	public static function error($message, array $context = []){
		self::getLogger()->addError($message, $context);
	}

	public static function critical($message, array $context = []){
		self::getLogger()->addCritical($message, $context);
	}

	public static function alert($message, array $context = []){
		self::getLogger()->addAlert($message, $context);
	}

	public static function emergency($message, array $context = []){
		self::getLogger()->addEmergency($message, $context);
	}

}