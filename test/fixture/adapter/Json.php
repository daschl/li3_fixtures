<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\test\fixture\adapter;

use lithium\data\Collection;
use RuntimeException;

/**
 * Parses a given json source file and returns it as an associative array.
 */
class Json extends \lithium\core\StaticObject {

	/**
	 * The file extension for the fixture files.
	 *
	 * @var string
	 */
	public static $extension = "json";

	/**
	 * Parses the file and returns it as an associative array.
	 *
	 * If an error occurs during the parsing process, a RuntimeException is raised instead of the
	 * array. This will then usually be shown in the Testing-Webinterface.
	 *
	 * @param string $file Filepath to the json file.
	 * @return array Fixtures as an associative array.
	 * @link http://php.net/manual/en/function.json-decode.php
	 */
	public static function parse($file) {
		$data = json_decode(file_get_contents($file), true);
		if (json_last_error() != JSON_ERROR_NONE) {
			throw new RuntimeException("Failed to parse json file `{$file}`");
		}
		return $data;
	}

	/**
	 * Encodes data in prep of saving to a file
	 *
	 * @param array $data
	 * @return string
	 */
	public static function encode($data) {
		if (self::phpVersion() >= 50400) {
			return json_encode($data, JSON_PRETTY_PRINT);
		}
		return self::prettyPrint(json_encode($data));
	}

	protected static function prettyPrint($json) {
		$indent = "  ";
		$pretty = "";
		$level = 1;
		$in_string = false;

		for($pos = 0; $pos < strlen($json); $pos++) {
			$char = $json[$pos];
			switch($char) {
				case '{':
				case '[':
					$pretty .= $char . ($in_string ? '' : "\n" . str_repeat($indent, $level));
					$in_string ?: $level++;
					break;
				case '}':
				case ']':
					$in_string ?: $level--;
					$pretty .= ($in_string ? '' : "\n" . str_repeat($indent, $level)) . $char;
					break;
				case ',':
					$pretty .= $char . ($in_string ? '' : "\n" . str_repeat($indent, $level));
					break;
				case ':':
					$pretty .= $char . ($in_string ? '' : ' ');
					break;
				case '"':
					if($pos > 0 && $json[$pos - 1] != '\\') {
						$in_string = !$in_string;
					}
				default:
					$pretty .= $char;
					break;
			}
		}
		return $pretty;
	}

	/**
	 * See: http://php.net/manual/en/function.phpversion.php
	 */
	protected static function phpVersion() {
		if (!defined('PHP_VERSION_ID')) {
			$version = explode('.', PHP_VERSION);
			define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
		}
		if (PHP_VERSION_ID < 50207) {
			define('PHP_MAJOR_VERSION',   $version[0]);
			define('PHP_MINOR_VERSION',   $version[1]);
			define('PHP_RELEASE_VERSION', $version[2]);
		}
		if (!defined('JSON_PRETTY_PRINT')) {
			define('JSON_PRETTY_PRINT', 0);
		}
		return PHP_VERSION_ID;
	}
}

?>