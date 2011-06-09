<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2011, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\test\source;

/**
 * Parses a given Json-File and returns it as an associative array.
 */
class Json extends \lithium\core\StaticObject {

	/**
	 * Parses the file and returns it as an associative array.
	 *
	 * If an error occurs during the parsing process, a RuntimeException is
	 * raised instead of the array. This will then usually be shown in the
	 * Testing-Webinterface.
	 *
	 * @param string $file Filepath to the json file.
	 * @return array Fixtures as an associative array.
	 * @link http://php.net/manual/en/function.json-decode.php
	 */
	public static function parse($file) {
		$data = json_decode(file_get_contents($file), true);
		if(json_last_error() != JSON_ERROR_NONE) {
			throw new \RuntimeException("Failed to parse json file `{$file}`");
		}
		return $data;
	}

}

?>