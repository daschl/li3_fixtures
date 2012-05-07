<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\tests\mocks\test\fixture\adapter;

/**
 * Mocks the Json Fixture Source.
 */
class MockJson extends \lithium\core\StaticObject {

	public static $extension = 'json';

	/**
	 * MockJson::parse returns an associative array of
	 * sample data for the FixtureTest-Class. Correct
	 * Json-File parsing is tested in the JsonTest.
	 *
	 * @param string $file Filepath to the json file.
	 * @return array Fixtures as an associative array.
	 * @link http://php.net/manual/en/function.json-decode.php
	 */
	public static function parse($file) {
		$data = array(
			'post1' => array(
				'title' => 'My First Post',
				'content' => 'First Content...'
			),
			'post2' => array(
				'title' => 'My Second Post',
				'content' => 'Also some foobar text'
			),
			'post3' => array(
				'title' => 'My Third Post',
				'content' => 'I like to write some foobar foo too'
			)
		);
		return $data;
	}

}

?>