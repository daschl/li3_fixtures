<?php
/**
 * li3_fixtures: Enhance your tests with Fixtures.
 *
 * @copyright     Copyright 2011, Michael Nitschinger (http://nitschinger.at)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_fixtures\test;

use lithium\util\Inflector;
use lithium\util\Collection;

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
 * More code examples and documentation can be found at the wiki.
 *
 * @see lithium\util\Collection
 * @link http://rad-dev.org/li3_fixtures
 */
class Fixture extends \lithium\core\StaticObject {

	/**
	 * Specifies the default values that get loaded.
	 * @var array
	 */
	protected static $_defaults = array(
		'path' => 'tests/fixtures',
		'type' => 'json',
		'sources' => array(),
		'collection' => 'Collection'
	);

	/**
	 * Contains all supported datasources. You can override/extend this in the
	 * Fixture::load()-call.
	 * @var array
	 */
	protected static $_sources = array(
		'json' => 'li3_fixtures\test\source\Json'
	);

	protected static $_collections = array(
		'Collection' => 'lithium\util\Collection',
		'DocumentSet'=> 'lithium\data\collection\DocumentSet',
		'DocumentArray' => 'lithium\data\collection\DocumentArray',
		'RecordSet' => 'lithium\data\collection\RecordSet'
	);

	/**
	 * Loads Fixture data and returns a Collection object.
	 *
	 * The load method loads the fixture file based on the $model param and then hands
	 * it over to the source parser (Json by default). After parsing, it returns the
	 * data as a Collection object. If you specify an optional collection parameter,
	 * this class will be used as the return class instead of lithiu\util\Collection.
	 *
	 * @param string $model The name of the model. It will be lowercased and pluralized
	 *											by the inflector.
	 * @param array $options Additional options can be specified here. Possible options are:
	 *											 - `path`: can be an absolute or relative path to the fixture file.
	 *											 - `type`: the extension of the fixture. defaults to json.
	 *											 - `sources`: add more parsing sources. Out of the box Json is used.
	 *											 - `collection`: a different collection. Defaults to lithium\util\Collection.
	 *														see static::$_collections for supported short hands or provide your own
	 *														fully namespaced classname (it has to be some kind of collection!)
	 * @return lithium\util\Collection A collection with all fixtures inside (or subclass from Collection).
	 * @see lithium\util\Collection
	 * @see lithium\data\collection\DocumentSet
	 * @see lithium\data\collection\DocumentArray
	 * @see lithium\data\collection\RecordSet
	 */
	public static function load($model, array $options = array()) {
		$options = $options + static::$_defaults;
		$sources = $options['sources'] + static::$_sources;
		$collection = false;

		if(!array_key_exists($options['type'], $sources)) {
			throw new \InvalidArgumentException("Unsupported type `".$options['type']."`");
		}

		if(substr($options['path'], 0, 1) != "/") {
			$options['path'] = LITHIUM_APP_PATH."/".$options['path'];
		}

		$model = strtolower(Inflector::pluralize($model));
		$file = $options['path']."/".$model.".".$options['type'];
		$source = $sources[$options['type']];

		if(file_exists($file) && is_readable($file)) {
			if(class_exists($options['collection'])) {
				$collection = $options['collection'];
			} elseif(isset(static::$_collections[$options['collection']])) {
				$collection = static::$_collections[$options['collection']];
			}
			if(!$collection) {
				throw new \InvalidArgumentException("Unsupported or empty collection given (`".$options['collection']."`)");
			}
			return new $collection(array('data' => $source::parse($file)));
		} else {
			throw new \RuntimeException("Could not read file `{$file}`");
		}

	}

}

?>