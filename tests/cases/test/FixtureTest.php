<?php
/**
 * li3_fixtures: Enrich your testing data with fixtures
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\tests\cases\test;

use li3_fixtures\test\Fixture;

/**
 * Tests the Fixture Class and also provides examples.
 */
class FixtureTest extends \lithium\test\Unit {

	/**
	 * Default load options (Mocks the Datasource)
	 */
	protected $_loadOptions = array(
		'library' => 'li3_fixtures',
		'adapter' => 'li3_fixtures\tests\mocks\test\fixture\adapter\MockJson'
	);

	/**
	 * Tests if a unavailable or unreadable file raises
	 * an Exception.
	 */
	public function testInvalidFileException() {
		$this->expectException('/Could not read file/');
		Fixture::load('Foobar');
	}

	/**
	 * Tests if a unsupported source type raises an Exception.
	 */
	public function testInvalidTypeException() {
		$options = array(
			'adapter' => 'foo'
		);
		$this->expectException('/Could not find adapter `Foo`/');
		Fixture::load('models/Posts', $options);
	}

	/**
	 * Test the correct loading procedure with mocked sources.
	 *
	 * It also provides usage examples. Note that the correct
	 * Json parsing tests are placed in the JsonTest-Class.
	 */
	public function testLoadWithCollection() {
		$options = $this->_loadOptions;
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Also test with DocumentSet instead of Collection.
	 */
	public function testLoadWithDocumentSet() {
		$options = $this->_loadOptions;
		$options['collection'] = 'DocumentSet';
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Also test with DocumentArray instead of Collection.
	 */
	public function testLoadWithDocumentArray() {
		$options = $this->_loadOptions;
		$options['collection'] = 'DocumentArray';
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Also test with RecordSet instead of Collection.
	 *
	 */
	public function testLoadWithRecordSet() {
		$options = $this->_loadOptions;
		$options['collection'] = 'RecordSet';
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Test Load with custom class.
	 */
	public function testLoadWithCustomCollection() {
		$options = $this->_loadOptions;
		$options['collection'] = 'lithium\util\Collection';
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Test Load without automatically wrapping the object into a Collection class.
	 */
	public function testLoadWithoutWrap() {
		$options = array(
			'class' => false,
			'adapter' => 'li3_fixtures\tests\mocks\test\fixture\adapter\MockPhp'
		) + $this->_loadOptions;
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Test Load with invalid collection name
	 */
	public function testInvalidCollectionParam() {
		$options = $this->_loadOptions;
		$options['collection'] = 'Foobar';
		$this->expectException('/Unsupported class given/');
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Test Load with invalid namespace class
	 */
	public function testInvalidClassParam() {
		$options = $this->_loadOptions;
		$options['class'] = 'Foo\bar';
		$this->expectException('/Unsupported class given/');
		$posts = Fixture::load('models/Posts', $options);
		$this->_testLoad($posts);
	}

	/**
	 * Helper method to shorten the assertions
	 */
	protected function _testLoad($posts) {
		$expected = array(
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

		$this->assertEqual($expected['post1'], $posts->first());
		$this->assertEqual($expected['post1'], $posts->current());
		$this->assertEqual($expected['post2'], $posts->next());
		$this->assertEqual($expected['post2'], $posts->current());
		$this->assertEqual($expected['post1'], $posts->prev());
		$this->assertEqual($expected['post2'], $posts->next());
		$this->assertEqual($expected['post3'], $posts->next());
		$this->assertEqual($expected['post2'], $posts->prev());
		$this->assertEqual($expected['post1'], $posts->rewind());
		$this->assertEqual($expected['post1'], $posts['post1']);
	}



	/**
	 * Test the types of the returned object. By default this is
	 * lithium\util\Collection, but other types are also supported
	 * and they should be tested accordingly (the have to be a
	 * sublcass of lithium\util\Collection in the current implementation).
	 */
	public function testLoadResultClass() {
		$path = dirname(dirname(__DIR__)).'/fixtures/{:file}.{:type}';
		$options = array('path' => $path);
		$ships = Fixture::load('models/Pirates', $options);
		$expected = 'lithium\util\Collection';
		$this->assertEqual($expected, get_class($ships));

		$options = array('path' => $path, 'collection' => 'Collection');
		$ships = Fixture::load('models/Pirates', $options);
		$expected = 'lithium\util\Collection';
		$this->assertEqual($expected, get_class($ships));

		$options = array('path' => $path, 'collection' => 'DocumentSet');
		$ships = Fixture::load('models/Pirates', $options);
		$expected = 'lithium\data\collection\DocumentSet';
		$this->assertEqual($expected, get_class($ships));

		$options = array('path' => $path, 'collection' => 'DocumentArray');
		$ships = Fixture::load('models/Pirates', $options);
		$expected = 'lithium\data\collection\DocumentArray';
		$this->assertEqual($expected, get_class($ships));

		$options = array('path' => $path, 'collection' => 'RecordSet');
		$ships = Fixture::load('models/Pirates', $options);
		$expected = 'lithium\data\collection\RecordSet';
		$this->assertEqual($expected, get_class($ships));

	}
}

?>