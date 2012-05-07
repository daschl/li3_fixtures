<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\test\fixture\adapter;

use RuntimeException;

/**
 * Parses a given php file and returns the data from it.
 */
class Php extends \lithium\core\StaticObject {

	/**
	 * The file extension for the fixture files
	 *
	 * @var string
	 */
	public static $extension = "php";

	/**
	 * Parses the file and returns it as an associative array.
	 *
	 * The php file is expected to contain one var named `$data` that is
	 * an associative array.
	 *
	 * If an error occurs during the parsing process, a RuntimeException is
	 * raised instead of the array. This will then usually be shown in the
	 * Testing-Webinterface.
	 *
	 * @param string $file Filepath to the json file.
	 * @return array Fixtures as an associative array.
	 */
	public static function parse($file) {
		include $file;
		if (!isset($data)) {
			throw new RuntimeException("Failed to parse php file `{$file}`");
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
		return "<?php\n\n\$data = " . var_export($data, true) . ";\n\n?>\n";
	}
}

?>