<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\tests\mocks\test\fixture\adapter;

use lithium\util\Collection;

/**
 * Mocks the Php Fixture Source.
 */
class MockPhp extends \lithium\core\StaticObject {

	public static $extension = 'php';

	/**
	 * Returns a associative array of
	 * sample data for the FixtureTest class. Also,
	 * returns an object.  If the options are set
	 * to not load the data into a collection, you
	 * can `var_export` any type into your fixture.
	 *
	 * @param string $file Filepath to the json file.
	 * @return array Fixtures as an associative array.
	 * @link http://php.net/manual/en/function.json-decode.php
	 */
	public static function parse($file) {
		return Collection::__set_state(array(
		   '_data' => 
		  array (
		    'post1' => 
		    array (
		      'title' => 'My First Post',
		      'content' => 'First Content...',
		    ),
		    'post2' => 
		    array (
		      'title' => 'My Second Post',
		      'content' => 'Also some foobar text',
		    ),
		    'post3' => 
		    array (
		      'title' => 'My Third Post',
		      'content' => 'I like to write some foobar foo too',
		    ),
		  ),
		   '_valid' => false,
		   '_autoConfig' => 
		  array (
		    0 => 'data',
		  ),
		   '_config' => 
		  array (
		    'init' => true,
		  ),
		   '_methodFilters' => 
		  array (
		  ),
		));
	}

}

?>