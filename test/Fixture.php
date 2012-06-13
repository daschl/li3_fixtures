<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2012, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\test;

use lithium\core\Libraries;
use lithium\util\Collection;
use lithium\util\Inflector;
use lithium\util\String;
use InvalidArgumentException;
use RuntimeException;

/**
 * With the Fixture Class you can add Fixtures to your tests.
 *
 * Fixtures are static files, which get loaded into arrays and can then be stored in the
 * database. They move large array definitions to external files and as a result make your
 * tests more readable and shorter. You can also write your own source to fetch data from
 * xml files or even web services.
 *
 * It returns a Collection Object, so you get all iterators and convenience methods for
 * free.
 *
 * Usage:
 * {{{
 * $fixtures = Fixture::load('Post');
 * // Returns a Collection from app/tests/fixtures/posts.json.
 *
 * $first_mock = $fixtures->first();
 * $second_mock = $fixtures->next();
 * // Assigns the post as arrays to the variables.
 *
 * $post = Post::create();
 * $post->save($fixtures->first());
 * // Stores the first fixture in the database.
 * }}}
 *
 * More code examples and documentation can be found in the readme.
 *
 * @see lithium\util\Collection
 * @link http://github.com/daschl/li3_fixtures
 */
class Fixture extends \lithium\core\Adaptable {

	/**
	 * Specifies the default values that get loaded.
	 * @var array
	 */
	protected static $_defaults = array(
		'adapter' => 'default',
		'cast' => true,
		'class' => 'Collection',
		'library' => true,
		'path' => '{:library}/tests/fixtures/{:file}.{:type}'
	);

	/**
	 * Libraries::locate() compatible path to adapters for this class.
	 *
	 * @see lithium\core\Libraries::locate()
	 * @var string Dot-delimited path.
	 */
	protected static $_adapters = 'fixture.test.fixture';

	/**
	 * Stores configurations for various fixture adapters.
	 *
	 * @var object `Collection` of fixture configurations.
	 */
	protected static $_configurations = array();

	/**
	 * A list of common classes to wrap your fixture data.
	 *
	 * @var array
	 */
	protected static $_classes = array(
		'Collection' => 'lithium\util\Collection',
		'DocumentSet'=> 'lithium\data\collection\DocumentSet',
		'DocumentArray' => 'lithium\data\collection\DocumentArray',
		'RecordSet' => 'lithium\data\collection\RecordSet'
	);

	/**
	 * Sets or gets the current adapter.
	 *
	 * @param string $name Class name of adapter to load.
	 * @return object Adapter object.
	 */
	public static function adapter($name = null) {
		if (!isset(static::$_configurations[$name])) {
			$config = array(
				'adapter' => strpos($name, '\\') === false ? ucfirst($name) : $name
			);
			if ($name === 'default') {
				$config = array(
					'adapter' => 'Json'
				);
			}
			static::$_configurations[$name] = $config;
		}
		return parent::adapter($name);
	}

	/**
	 * Loads Fixture data.
	 *
	 * The load method loads the fixture file based on the $file param and then hands it over to
	 * the source parser (json by default). After parsing, it returns the data. If you specify an
	 * optional class parameter, if will wrap the data in the class and pass the data into the
	 * class constructor's data param. This is compatible with the way how Lithium `Collection`
	 * classes work.
	 *
	 * @param string $file The name of the file without the file extension.
	 * @param array $options Additional options can be specified here. Possible options are:
	 *		- `adapter`: the adapter to use to load the fixture
	 *		- `class` : a class to wrap the data in
	 *		- `library` : look for the fixtures in a different library
	 *		- `path` : String-insert style file path
	 *		- `sources`: add more parsing sources. Out of the box Json is used.
	 * @return array|object The array of data, optionally wrapped in a class such as
	 * 		`lithium\util\Collection`.
	 */
	public static function load($file, array $options = array()) {
		$options = $options + static::$_defaults;
		if (isset($options['collection'])) {
			// backwards compatibility
			$options['class'] = $options['collection'];
		}
		$class = false;

		$options['adapter'] = $adapter = static::adapter($options['adapter']);
		$file = static::file($file, $options);

		if (file_exists($file) && is_readable($file) && !is_dir($file)) {
			$data = $adapter::parse($file);
			if ($options['class'] === false) {
				return $data;
			}
			if (class_exists($options['class'])) {
				$class = $options['class'];
			} else if (isset(static::$_classes[$options['class']])) {
				$class = static::$_classes[$options['class']];
			}
			if (!$class) {
				throw new InvalidArgumentException("Unsupported class given (`{$options['class']}`)");
			}
			return new $class(compact('data'));
		} else {
			throw new RuntimeException("Could not read file `{$file}`");
		}
	}

	/**
	 * Saves data to a fixture file.
	 *
	 * @param string $file The name of the file. It will be lowercased and slugified
	 *                      by the inflector.  Directory separators will be preserved.
	 * @param object|array $data If an instance of a Collection, data will
	 * @param array $options Additional options can be specified here. Possible options are:
	 *		- `adapter`: the adapter to use to load the fixture
	 *		- `cast` : set to false to prevent `Collection` being converted to arrays.
	 *		- `library` : save the fixtures in a different library
	 *		- `path`: can be an absolute or relative path to the fixture file.
	 * @return boolean Returns whether the file saving was successful or not.
	 */
	public static function save($file, $data, array $options = array()) {
		$options = $options + static::$_defaults;

		$options['adapter'] = $adapter = static::adapter($options['adapter']);
		$file = static::file($file, $options);
		$dir = dirname($file);

		if (!file_exists($dir)) {
			mkdir($dir, 0775, true);
		}

		if (file_exists($file) && !is_writable($file)) {
			throw new RuntimeException("Could not write file `{$file}`");
		}

		if ($options['cast'] && $data instanceof Collection) {
			$data = $data->to('array');
		}

		if (empty($data)) {
			return false;
		}
		$data = $adapter::encode($data);
		return file_put_contents($file, $data) ? true : false;
	}

	/**
	 * Returns the path to a fixture file.
	 *
	 * @param string $file The filepath of the fixture file.
	 * @param array $options Additional options to pass.
	 * @see li3_fixtures\test\Fixture::load()
	 */
	public static function file($file, array $options = array()) {
		if (empty($options)) {
			$options = static::$_defaults;
		}
		if (!isset($options['adapter']) || !is_object($options['adapter'])) {
			$options['adapter'] = static::adapter($options['adapter']);
		}
		$adapter = $options['adapter'];
		$options['library'] = Libraries::get($options['library'], 'path');
		$pieces = explode("/", $file);
		$pieces = array_map(function($file) {
			return strtolower(Inflector::slug($file));
		}, $pieces);
		$options['file'] = implode("/", $pieces);
		$options['type'] = $options['adapter']::$extension;
		return String::insert($options['path'], $options);
	}

}

?>