# Fixtures for Lithium
The li3_fixtures plugin helps you to simplify your tests by removing the need for large static content arrays in the setUp() method. For the moment, only JSON files as fixtures are supported, but other sources are planned for the upcoming release.

## Installation
The installation process is simple, just place the source code in the libraries-folder and create a fixtures directory.

    $ cd /path/to/app/libraries
    $ git clone git@github.com:daschl/li3_fixtures.git
    $ mkdir /path/to/app/tests/fixtures

Now, in your `app/config/bootstrap/libraries.php` add this to the bottom (you may optionally place an environment check around this, to avoid loading the fixtures plugin in a production environment):

    Libraries::add('li3_fixtures');

## Examples
Here are some examples, more information will be added soon. Don't forget to include the Fixtures with `use li3_fixtures\test\Fixture;` and the top of your test files.

    // loads app/tests/fixtures/posts.json
    $fixtures = Fixture::load('Post');
    
    // store the first post fixture in the database
    $post = Post::create();
    $post->save($fixtures->first());

Basically, `Fixture::load()` returns a `Collection`-Object (or a derivate of it), which provides you convenience methods like `first()`, `next()`, `current()`, `prev()`, `last()` and so on. For more information, see the documentation for the `lithium\util\Collection` class.

## JSON Fixtures
The PHP-Json parser is very strict, so you will encounter an exception when your fixtures are not correct. Here is a valid example file, that you can modify and extend. Also, check out the JSON specification at http://www.json.org/.

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