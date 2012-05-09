# Fixtures for Lithium
The li3_fixtures plugin helps you to simplify your tests by removing the need for
large static content arrays in the setUp() method. For the moment, json and
php adapters are supported.  It's easy to write adapters (xml, yaml) so
send us a pull request!

## Installation
The installation process is simple, just place the source code in the
libraries-directory and create a fixtures directory:

    $ cd /path/to/app/libraries
    $ git clone git@github.com:daschl/li3_fixtures.git
    $ mkdir /path/to/app/tests/fixtures

Now, in your `app/config/bootstrap/libraries.php` add this to the bottom
(you may optionally place an environment check around this, to avoid
loading the fixtures plugin in a production environment):

    Libraries::add('li3_fixtures');

Don't forget to include the Fixtures with `use li3_fixtures\test\Fixture;`
at the top of your test files.

## Examples

### Loading Collections

    // loads app/tests/fixtures/models/posts.json
    $fixtures = Fixture::load('models/Posts');
    
    // store the first post fixture in the database
    $post = Posts::create();
    $post->save($fixtures->first());

By default, `Fixture::load()` gets array data from the source file and
returns a `Collection` object (or a derivate of it),
which provides you convenience methods like `first()`, `next()`, `current()`,
`prev()`, `last()` and so on. For more information, see the documentation
for the `lithium\util\Collection` class.

### Saving Fixtures

	use lithium\util\Inflector;

	// let's assume $request exists and is a Request object
	// the url is "/posts"
	$file = 'requests/' . Inflector::slug(trim($request->url, '/'));
	Fixtures::save($request, $file, array('type' => 'php'));

Using the php source, you can take a real `Request` object, save it to a fixture
and then use it later in your tests.  This reduces the potential for human
error when creating mock classes in tests.

### Loading Custom Objects

    // loads app/tests/fixtures/requests/posts.php
    $request = Fixture::load('requests/posts', array('type' => 'php'));

The save example above writes an exported version of the `Request` object
to the fixture file.  The load example here loads the `Request` object.

## JSON Fixtures
The PHP-Json parser is very strict, so you will encounter an exception when
your fixtures are not correct. Here is a valid example file, that you can
modify and extend. Also, check out the JSON specification at http://www.json.org/.

    {
        "pearl": {
            "name": "The Black Pearl",
            "captain": "Jack Sparrow",
            "type": "East Indiaman",
            "appearances": [
                "The Course of the Black Pearl",
                "Dead Man's Chest At World's End"
           ]
        }
    }

## PHP Fixtures
The php file should contain one var named `$data` that is the associative
array with the fixture data.  This file is simply included, so it could
be possible to do some manipulation in php of the data (however, keep
in mind this is a "fixture" which means the data shouldn't change).
The main reason for using plain php (encoded by the `Fixture::save()`
method via `var_export()`) is to give maximum flexibility to the 
programmer to even be able to run php code inside the fixture.

    <?php

    $data = array(
        "pearl" => array(
            "name" => "The Black Pearl",
            "captain" => "Jack Sparrow",
            "type" => "East Indiaman",
            "appearances" => array(
                "The Course of the Black Pearl",
                "Dead Man's Chest At World's End"
            )
            "name" => "The Black Pearl"
        )
    );
    
    ?>

## Custom Adapters

You can easily write your own fixture adapter.  It needs to have two static methods:
- parse(): should take the contents of the fixture file and convert it to an
    associative array.
- encode(): converts data into the format of the fixture.

You also need to define a static var `$extension` which contains which file
extension to use when finding fixture files.

Custom adapters should be placed in `extensions/adapters/test/fixture` inside
your app directory.

    <?php
    
    namespace app\extensions\adapters\test\fixture;
    
    class Yaml {
    
    	public static $extension = "yml";
    
    	public static function parse($file) {
    		return yaml_parse($file);
    	}
    
    	public static function encode($data) {
    		return yaml_emit($data);
    	}
    
    }
    
    ?>

