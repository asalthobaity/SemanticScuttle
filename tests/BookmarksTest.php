<?php
require_once 'PHPUnit/Framework.php';

/*
 To launch this test, type the following line into a shell
 into the tests/ directory :
     phpunit BookmarksTest tests/boomarksTest.php
 */

class BookmarksTest extends PHPUnit_Framework_TestCase
{
	protected $us;
	protected $bs;
	protected $ts;
	protected $tts;

	protected function setUp()
	{
		global $dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbpersist, $dbtype,
            $tableprefix, $TEMPLATES_DIR, $filetypes, $debugMode;
		require_once dirname(__FILE__) . '/../src/SemanticScuttle/header.php';

		$this->us =SemanticScuttle_Service_Factory::get('User');
		$this->bs =SemanticScuttle_Service_Factory::get('Bookmark');
		$this->bs->deleteAll();
		$this->b2ts=SemanticScuttle_Service_Factory::get('Bookmark2Tag');
		$this->b2ts->deleteAll();
		$this->tts =SemanticScuttle_Service_Factory::get('Tag2Tag');
		$this->tts->deleteAll();
		$this->tsts =SemanticScuttle_Service_Factory::get('TagStat');
		$this->tsts->deleteAll();
	}

	public function testHardCharactersInBookmarks()
	{		
		$bs = $this->bs;
		$title = "title&é\"'(-è_çà)=";
		$desc = "description#{[|`\^@]}³<> ¹¡÷×¿&é\"'(-è\\_çà)=";
		$tag1 = "#{|`^@]³¹¡¿<&é\"'(-è\\_çà)";	
		$tag2 = "&é\"'(-è.[?./§!_çà)";

		$bs->addBookmark("http://site1.com", $title, $desc, "status", array($tag1, $tag2), null, false, false, 1);

		$bookmarks =& $bs->getBookmarks(0, 1, NULL, NULL, NULL, getSortOrder(), NULL, 0);

		$b0 = $bookmarks['bookmarks'][0];
		$this->assertEquals($title, $b0['bTitle']);
		$this->assertEquals($desc, $b0['bDescription']);
		$this->assertEquals(str_replace(array('"', '\'', '/'), "_", $tag1), $b0['tags'][0]);
		$this->assertEquals(str_replace(array('"', '\'', '/'), "_", $tag2), $b0['tags'][1]);
	}

	public function testUnificationOfBookmarks()
	{		
		$bs = $this->bs;

		$bs->addBookmark("http://site1.com", "title", "description", "status", array('tag1'), null, false, false, 1);
		$bs->addBookmark("http://site1.com", "title2", "description2", "status", array('tag2'), null, false, false, 2);

		$bookmarks =& $bs->getBookmarks(0, 1, NULL, NULL, NULL, getSortOrder(), NULL, 0);
		$this->assertEquals(1, $bookmarks['total']);
	}

	/*public function testSearchingBookmarksAccentsInsensible()
	 {
	 $bs = $this->bs;

	 $bs->addBookmark("http://site1.com", "title", "éèüaàê", "status", array('tag1'), null, false, false, 1);
	 $bookmarks =& $bs->getBookmarks(0, NULL, NULL, NULL, $terms = "eeaae"); //void
	 $this->assertEquals(0, $bookmarks['total']);
	 $bookmarks =& $bs->getBookmarks(0, NULL, NULL, NULL, $terms = "eeuaae");
	 $this->assertEquals(1, $bookmarks['total']);
	 }*/

}
?>