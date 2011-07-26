<?php
/**
 * li3_fixtures: Enrich your testing data with fixtures
 *
 * @copyright     Copyright 2011, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\tests\integration\test;

use li3_fixtures\test\Fixture;

/**
 * Tests the Fixture Plugin as a whole.
 */
class FixtureTest extends \lithium\test\Integration {

	/**
	 * Holds options for all tests
	 */
	protected $_options = array();

	/**
	 * Sets some options that are needed throughout the tests.
	 */
	public function setUp() {
		$this->_options['path'] = dirname(dirname(__DIR__)).'/fixtures';
	}

	/**
	 * Tests the Load Method Full Stack
	 */
	public function testLoad() {
		$ships = Fixture::load('Pirate', $this->_options);

		$expected = 'lithium\util\Collection';
		$this->assertEqual($expected, get_class($ships));

		$working = false;
		$ships_array = $ships->to('array');
		if(is_array($ships_array) && !empty($ships_array)) {
			$working = true;
		}
		$this->assertTrue($working);

		$this->assertEqual(count($ships), count($ships_array));
		$this->assertEqual($ships['pearl'], $ships_array['pearl']);
		$this->assertEqual($ships->first(), $ships_array['pearl']);
		foreach($ships As $key => $ship) {
			if($key == 'pearl') {
				$this->assertEqual('The Black Pearl', $ship['name']);
			}
		}
	}

}

?>