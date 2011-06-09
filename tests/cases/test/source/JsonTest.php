<?php
/**
 * li3_fixtures: Enrich your testing data with fixtures
 *
 * @copyright     Copyright 2011, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\tests\cases\test\source;

use li3_fixtures\test\source\Json;

/**
 * Tests the Json Fixture Source with a more or less complex
 * Json-File.
 */
class JsonTest extends \lithium\test\Unit {

	/**
	 * Tests correct parsin with a sample json file. It tests
	 * strings and arrays.
	 */
	public function testCorrectParse() {
		$file = dirname(__FILE__).'/../../../fixtures/pirates.json';
		$expected = array(
			"pearl" => array(
				"name" => "The Black Pearl",
				"captain" => "Jack Sparrow",
				"type" => "East Indiaman",
				"appearances" => array(
					"The Course of the Black Pearl",
					"Dead Man's Chest At World's End"
				)
			)
		);
		$result = Json::parse($file);
		$this->assertEqual($expected, $result);
	}

	/**
	 * Tests if a invalid json file raises an Exception.
	 */
	public function testParseErrorException() {
		$file = dirname(__FILE__).'/../../../fixtures/pirates_invalid.json';
		$this->expectException('/Failed to parse json file/');
		$result = Json::parse($file);
	}

}

?>