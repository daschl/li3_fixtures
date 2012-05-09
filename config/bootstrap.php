<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;

if (Libraries::paths('fixture') === null) {
	Libraries::paths(array(
		'fixture' => array(
			'{:library}\extensions\adapter\{:namespace}\{:class}\{:name}',
			'{:library}\{:namespace}\{:class}\adapter\{:name}' => array('libraries' => 'li3_fixtures')
		)
	));
}

?>