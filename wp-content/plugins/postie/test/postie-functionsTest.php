<?php

require_once 'wpstub.php';
require_once '../postie-functions.php';
require_once '../simple_html_dom.php';
require_once '../postie.php';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('POSTIE_DEBUG', true);

class postiefunctionsTest extends PHPUnit_Framework_TestCase {

    public function testAllowCommentsOnPost() {
        $original_content = "test content, no comment control";
        $modified_content = "test content, no comment control";
        $allow = tag_AllowCommentsOnPost($modified_content);
        $this->assertEquals("open", $allow);
        $this->assertEquals($original_content, $modified_content);

        $original_content = "test content, comment control closed ";
        $modified_content = "test content, comment control closed comments:0";
        $allow = tag_AllowCommentsOnPost($modified_content);
        $this->assertEquals("closed", $allow);
        $this->assertEquals($original_content, $modified_content);

        $original_content = "test content, comment control open ";
        $modified_content = "test content, comment control open comments:1";
        $allow = tag_AllowCommentsOnPost($modified_content);
        $this->assertEquals("open", $allow);
        $this->assertEquals($original_content, $modified_content);

        $original_content = "test content, comment control registered only ";
        $modified_content = "test content, comment control registered only comments:2";
        $allow = tag_AllowCommentsOnPost($modified_content);
        $this->assertEquals("registered_only", $allow);
        $this->assertEquals($original_content, $modified_content);
    }

    public function testBannedFileName() {
        $this->assertFalse(isBannedFileName("", null));
        $this->assertFalse(isBannedFileName("", ""));
        $this->assertFalse(isBannedFileName("", array()));
        $this->assertFalse(isBannedFileName("test", array()));
        $this->assertTrue(isBannedFileName("test", array("test")));
        $this->assertFalse(isBannedFileName("test", array("test1")));
        $this->assertTrue(isBannedFileName("test.exe", array("*.exe")));
        $this->assertFalse(isBannedFileName("test.pdf", array("*.exe")));
        $this->assertFalse(isBannedFileName("test.pdf", array("*.exe", "*.js", "*.cmd")));
        $this->assertFalse(isBannedFileName("test.cmd.pdf", array("*.exe", "*.js", "*.cmd")));
        $this->assertTrue(isBannedFileName("test test.exe", array("*.exe")));
    }

    public function testCheckEmailAddress() {
        $this->assertFalse(isEmailAddressAuthorized(null, null));
        $this->assertFalse(isEmailAddressAuthorized(null, array()));
        $this->assertFalse(isEmailAddressAuthorized("", array()));
        $this->assertFalse(isEmailAddressAuthorized("", array("")));
        $this->assertFalse(isEmailAddressAuthorized("bob", array("jane")));
        $this->assertTrue(isEmailAddressAuthorized("bob", array("bob")));
        $this->assertTrue(isEmailAddressAuthorized("bob", array("BoB")));
        $this->assertTrue(isEmailAddressAuthorized("bob", array("bob", "jane")));
        $this->assertTrue(isEmailAddressAuthorized("bob", array("jane", "bob")));
    }

    public function testConvertUTF8ToISO_8859_1() {
        $this->assertEquals("test", ConvertUTF8ToISO_8859_1("random", "stuff", "test"));
        $this->assertEquals("Phasa Thai", ConvertUTF8ToISO_8859_1('quoted-printable', 'iso-8859-1', "Phasa Thai"));
        $this->assertEquals("ภาษาไทย Phasa Thai", ConvertUTF8ToISO_8859_1('quoted-printable', 'tis-620', "=C0=D2=C9=D2=E4=B7=C2 Phasa Thai"));
        $this->assertEquals("??????? Phasa Thai", ConvertUTF8ToISO_8859_1('base64', 'utf-8', "ภาษาไทย Phasa Thai"));
        $this->assertEquals("ภาษาไทย Phasa Thai", ConvertUTF8ToISO_8859_1('something', 'utf-8', "ภาษาไทย Phasa Thai"));
        $this->assertEquals("ภาษาไทย Phasa Thai", ConvertUTF8ToISO_8859_1('base64', 'iso-8859-1', "ภาษาไทย Phasa Thai"));
    }

    public function testConvertToUTF_8() {
        $this->assertEquals("に投稿できる", ConvertToUTF_8('iso-2022-jp', iconv("UTF-8", "ISO-2022-JP", "に投稿できる")));
        $this->assertEquals("Код Обмена Информацией, 8 бит", ConvertToUTF_8('koi8-r', iconv("UTF-8", "koi8-r", "Код Обмена Информацией, 8 бит")));
    }

    public function testfilter_Delay() {
        $content = "test";
        $r = filter_Delay($content);
        $this->assertTrue(is_array($r));
        $this->assertEquals(3, count($r));
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test", $content);

        $content = "test delay:";
        $r = filter_Delay($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test delay:", $content);

        $content = "test delay:1h";
        $r = filter_Delay($content);
        $this->assertEquals(3600, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:1d";
        $r = filter_Delay($content);
        $this->assertEquals(86400, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:1m";
        $r = filter_Delay($content);
        $this->assertEquals(60, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:m";
        $r = filter_Delay($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:dhm";
        $r = filter_Delay($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:x";
        $r = filter_Delay($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test delay:x", $content);

        $content = "test delay:-1m";
        $r = filter_Delay($content);
        $this->assertEquals(-60, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:1d1h1m";
        $r = filter_Delay($content);
        $this->assertEquals(90060, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:d1hm";
        $r = filter_Delay($content);
        $this->assertEquals(3600, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test";
        $r = filter_Delay($content, '2012-11-20 08:00', 1);
        $this->assertEquals('2012-11-20 17:00:00', $r[0]);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test", $content);
    }

    public function testfilter_Start() {
        $config = config_GetDefaults();

        $c = "test";
        filter_Start($c, $config);
        $this->assertEquals("test", $c);

        $c = ":start\ntest";
        filter_Start($c, $config);
        $this->assertEquals("\ntest", $c);

        $c = "test/n:start\nsomething";
        filter_Start($c, $config);
        $this->assertEquals("\nsomething", $c);

        $c = "<p>test</p><p>:start</p><p>something</p>";
        filter_Start($c, $config);
        $this->assertEquals("</p><p>something</p>", $c);
    }

    public function testfilter_End() {
        $config = config_GetDefaults();
        $c = "test";
        filter_End($c, $config);
        $this->assertEquals("test", $c);

        $c = "test :end";
        filter_End($c, $config);
        $this->assertEquals("test ", $c);

        $c = "test :end test";
        filter_End($c, $config);
        $this->assertEquals("test ", $c);

        $c = "tags: Station, Kohnen, Flugzeug\n:end\n21.10.2012";
        filter_End($c, $config);
        $this->assertEquals("tags: Station, Kohnen, Flugzeug\n", $c);

        $c = "This is a test :end";
        filter_End($c, $config);
        $this->assertEquals("This is a test ", $c);

        $c = "<p>This is a test</p><p>:end</p><div>some footer</div>";
        filter_End($c, $config);
        $this->assertEquals("<p>This is a test</p><p>", $c);
    }

    public function testfilter_Newlines() {
        $config = config_GetDefaults();

        $c = "test";
        filter_Newlines($c, $config);
        $this->assertEquals("test", $c);

        $c = "test";
        filter_Newlines($c, $config);
        $this->assertEquals("test", $c);

        $c = "test\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test ", $c);

        $c = "test\r\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test ", $c);

        $c = "test\r";
        filter_Newlines($c, $config);
        $this->assertEquals("test ", $c);

        $c = "test\n\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test ", $c);

        $c = "test\r\n\r\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test  ", $c);

        $c = "test\r\n\r\ntest\n\ntest\rtest\r\ntest\ntest";
        filter_Newlines($c, $config);
        $this->assertEquals("test  test test test test test", $c);

        $config['convertnewline'] = true;

        $c = "test\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test<br />\n", $c);

        $c = "test\n\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test<br />\n", $c);

        $c = "test\r";
        filter_Newlines($c, $config);
        $this->assertEquals("test<br />\n", $c);

        $c = "test\r\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test<br />\n", $c);

        $c = "test\r\n\r\n";
        filter_Newlines($c, $config);
        $this->assertEquals("test<br />\n<br />\n", $c);

        $c = "test\r\n\r\ntest\n\ntest\rtest\r\ntest\ntest";
        filter_Newlines($c, $config);
        $this->assertEquals("test<br />\n<br />\ntest<br />\ntest<br />\ntest<br />\ntest<br />\ntest", $c);
    }

    public function testGetNameFromEmail() {
        $this->assertEquals("", GetNameFromEmail(""));
        $this->assertEquals("Wayne", GetNameFromEmail('Wayne <wayne@devzing.com>'));
        $this->assertEquals("wayne", GetNameFromEmail('wayne@devzing.com'));
    }

    public function testGetPostType() {
        $pm = new PostiePostModifiers();
        $subject = "test";
        $this->assertEquals("post", tag_PostType($subject, $pm));
        $this->assertEquals("test", $subject);

        $subject = "custom//test";
        $this->assertEquals("custom", tag_PostType($subject, $pm));
        $this->assertEquals("test", $subject);

        $subject = "//test";
        $this->assertEquals("post", tag_PostType($subject, $pm));
        $this->assertEquals("//test", $subject);

        $subject = "//";
        $this->assertEquals("post", tag_PostType($subject, $pm));
        $this->assertEquals("//", $subject);

        $subject = "custom2//test";
        $this->assertEquals("custom2", tag_PostType($subject, $pm));
        $this->assertEquals("test", $subject);

        $subject = "Custom1 // test";
        $this->assertEquals("custom1", tag_PostType($subject, $pm));
        $this->assertEquals("test", $subject);

        $subject = "video//test";
        $this->assertEquals("post", tag_PostType($subject, $pm));
        $this->assertEquals("test", $subject);
        $this->assertEquals('video', $pm->PostFormat);
        
        $subject = "//WL2K /Test Message";
        $this->assertEquals("post", tag_PostType($subject, $pm));
        $this->assertEquals("//WL2K /Test Message", $subject);
        $this->assertEquals('video', $pm->PostFormat);
    }

    public function testGetPostExcerpt() {
        $c = "test";
        $this->assertEquals("", tag_Excerpt($c, false, false));

        $c = ":excerptstart test :excerptend test";
        $this->assertEquals("test ", tag_Excerpt($c, false, false));

        $c = ":excerptstart test";
        $this->assertEquals("", tag_Excerpt($c, false, false));

        $c = "test :excerptend test";
        $this->assertEquals("", tag_Excerpt($c, false, false));
    }

    public function testGetPostCategories() {
        global $wpdb;
        global $g_get_term_by;

        $s = "test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("test", $s);

        $s = ":test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("default", $c[0]);
        $this->assertEquals(":test", $s);

        $g_get_term_by->term_id = 1;
        $s = "1: test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("1", $c[0]);
        $this->assertEquals("test", $s);

        $g_get_term_by = false;
        $s = "not a category: test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("not a category: test", $s);

        $s = "[not a category] test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("[not a category] test", $s);

        $s = "-not a category- test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("-not a category- test", $s);

        $g_get_term_by = new stdClass();
        $g_get_term_by->term_id = 1;
        $s = "general: test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals(1, $c[0]);
        $this->assertEquals("test", $s);

        $s = "[general] test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals(1, $c[0]);
        $this->assertEquals("test", $s);

        $s = "-general- test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals(1, $c[0]);
        $this->assertEquals("test", $s);

        $g_get_term_by = false;
        $s = "specific: test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("specific: test", $s);

        $g_get_term_by = new stdClass();
        $g_get_term_by->term_id = 1;
        $s = "[1] [1] test";
        $c = tag_categories($s, "default", false);
        $this->assertEquals(2, count($c));
        $this->assertEquals("1", $c[0]);
        $this->assertEquals("1", $c[1]);
        $this->assertEquals("test", $s);
    }

    public function testHTML2HTML() {
        $this->assertEquals("", filter_CleanHtml(""));
        $this->assertEquals("test", filter_CleanHtml("test"));
        $this->assertEquals("<div>test</div>\n", filter_CleanHtml("<html lang='en'><body>test</body></html>"));
        $this->assertEquals("<div>test</div>\n", filter_CleanHtml("<html lang='en'><head><title>title</title></head><body>test</body></html>"));
        $this->assertEquals("<div>test</div>\n", filter_CleanHtml("<body>test</body>"));
        $this->assertEquals("<strong>test</strong>", filter_CleanHtml("<strong>test</strong>"));
    }

    public function testSafeFileName() {
        $this->assertEquals("testtest", SafeFileName('test\/:*?"<>|test'));
    }

    public function testremove_signature() {
        $config = config_GetDefaults();

        $c = "";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("", $c);

        $c = "test";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("test", $c);

        $c = "";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("", $c);

        $c = "test";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("test", $c);

        $c = "line 1\nline 2\n--\nsig line 1\nsig line 2";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("line 1\nline 2\n", $c);

        $c = "line 1\nline 2\n---\nsig line 1\nsig line 2";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("line 1\nline 2\n", $c);

        $c = "line 1\nline 2\n-- \nsig line 1\nsig line 2";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("line 1\nline 2\n", $c);

        $c = "line 1\nline 2\n--\nsig line 1\nsig line 2";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("line 1\nline 2\n", $c);

        $c = "line 1\nline 2\n--";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("line 1\nline 2\n", $c);

        $c = "test content<div><br></div><div>--</div><div>signature</div>";
        filter_RemoveSignature($c, $config);
        $this->assertEquals("test content<div><br></div>", $c);
    }

    public function testmore_reccurences() {
        $sched = array();
        $newsched = postie_more_reccurences($sched);
        $this->assertEquals(4, count($newsched));
    }

    public function testpostie_get_tags() {
        $c = "";
        $t = tag_Tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("", $c);

        $c = "test";
        $t = tag_Tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("test", $c);

        $c = "test";
        $t = tag_Tags($c, array("tag1"));
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test", $c);

        $c = "test tags:";
        $t = tag_Tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("test tags:", $c);

        $c = "test tags:\n";
        $t = tag_Tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("test tags:\n", $c);

        $c = "test tags: tag1";
        $t = tag_Tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("test ", $c);

        $c = "test\ntags: tag1";
        $t = tag_Tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("test\n", $c);

        $c = "test tags: tag1\n";
        $t = tag_Tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test \n", $c);

        $c = "test tags:tag1";
        $t = tag_Tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test ", $c);

        $c = "test tags:tag1";
        $t = tag_Tags($c, array("tagx"));
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test ", $c);

        $c = "test tags:tag1,tag2";
        $t = tag_Tags($c, "");
        $this->assertEquals(2, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("tag2", $t[1]);
        $this->assertEquals("test ", $c);

        $c = "test tags: tag3,tag4\nmore stuff\n:end";
        $t = tag_Tags($c, "");
        $this->assertEquals(2, count($t));
        $this->assertEquals("tag3", $t[0]);
        $this->assertEquals("tag4", $t[1]);
        $this->assertEquals("test \nmore stuff\n:end", $c);

        $c = "test tags:tag1,tag2\nmore stuff\n:end";
        $t = tag_Tags($c, "");
        $this->assertEquals(2, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("tag2", $t[1]);
        $this->assertEquals("test \nmore stuff\n:end", $c);
    }

    public function testfilter_linkify() {
        $this->assertEquals("", filter_linkify(""));
        $this->assertEquals("test", filter_linkify("test"));
        $this->assertEquals('<a href="http://www.example.com" >http://www.example.com</a>', filter_linkify("http://www.example.com"));
        $this->assertEquals('<a href="http://www.example.com">www.example.com</a>', filter_linkify("www.example.com"));
        $this->assertEquals('<a href="http://www.example.com">www.example.com</a> <a href="http://www.example.com">www.example.com</a>', filter_linkify("www.example.com www.example.com"));
        $this->assertEquals('<a href="mailto:bob@example.com">bob@example.com</a>', filter_linkify("bob@example.com"));
        $this->assertEquals("<img src='http://www.example.com'/>", filter_linkify("<img src='http://www.example.com'/>"));
        $this->assertEquals("<html><head><title></title></head><body><img src='http://www.example.com'/></body></html>", filter_linkify("<html><head><title></title></head><body><img src='http://www.example.com'/></body></html>"));
        $this->assertEquals('<html><head><title></title></head><body><img src="http://www.example.com"/><a href="http://www.example.com">www.example.com</a></body></html>', filter_linkify('<html><head><title></title></head><body><img src="http://www.example.com"/>www.example.com</body></html>'));
        $this->assertEquals("<img src='http://www.example.com'/>", filter_linkify("<img src='http://www.example.com'/>"));
    }

    public function testfilter_Videos() {
        $this->assertEquals("video\ntest", filter_Videos("video\ntest"));
        $this->assertEquals("A youtube link <embed width='425' height='344' allowfullscreen='true' allowscriptaccess='always' type='application/x-shockwave-flash' src='http://www.youtube.com/v/oAguHwl9Vzq&hl=en&fs=1' />", filter_Videos("A youtube link https://www.youtube.com/watch?v=oAguHwl9Vzq", false));
        $this->assertEquals("A youtube link [youtube oAguHwl9Vzq]", filter_Videos("A youtube link https://www.youtube.com/watch?v=oAguHwl9Vzq", true));
    }

    public function testtag_Date() {
        $c = "";
        $this->assertEquals(null, tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "date:";
        $this->assertEquals(null, tag_Date($c, null, 0));
        $this->assertEquals("date:", $c);

        $c = "date: nothing";
        $this->assertEquals(null, tag_Date($c, null, 0));
        $this->assertEquals("date: nothing", $c);

        $c = "date: 1";
        $this->assertEquals(null, tag_Date($c, null, 0));
        $this->assertEquals("date: 1", $c);

        $c = "date: 12/31/2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "date:12/31/2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "Date: 12/31/2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "DATE: 12/31/2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "date: 31-12-2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "date: 31.12.2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "date: Dec 31, 2013";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "date: 12/31/2013\nstuff";
        $this->assertEquals("2013-12-31", tag_Date($c, null, 0));
        $this->assertEquals("\nstuff", $c);

        $c = "date: Dec 31, 2013 14:22";
        $this->assertEquals("2013-12-31 14:22:00", tag_Date($c, null, 0));
        $this->assertEquals("", $c);

        $c = "stuff\n\ndate: Dec 31, 2013 14:22\n\nmorestuff";
        $this->assertEquals("2013-12-31 14:22:00", tag_Date($c, null, 0));
        $this->assertEquals("stuff\n\n\n\nmorestuff", $c);

        $c = "<p>stuff</p><p>date: Dec 31, 2013 14:22</p><p>morestuff</p>";
        $this->assertEquals("2013-12-31 14:22:00", tag_Date($c, null, 0));
        $this->assertEquals("<p>stuff</p><p></p><p>morestuff</p>", $c);
    }

    function testtag_Excerpt() {
        $c = "";
        $e = tag_Excerpt($c, false, false);
        $this->assertEquals("", $c);
        $this->assertEquals("", $e);

        $c = ":excerptstart stuff";
        $e = tag_Excerpt($c, false, false);
        $this->assertEquals(":excerptstart stuff", $c);
        $this->assertEquals("", $e);

        $c = "stuff :excerptend";
        $e = tag_Excerpt($c, false, false);
        $this->assertEquals("stuff :excerptend", $c);
        $this->assertEquals("", $e);

        $c = ":excerptstart stuff :excerptend";
        $e = tag_Excerpt($c, false, false);
        $this->assertEquals("", $c);
        $this->assertEquals("stuff ", $e);
    }

    function test_filename_fix() {
        $f = "simple.png";
        $this->assertEquals($f, filename_fix($f));

        $f = "moiré-pättern.png";
        $this->assertEquals("moireCC81-paCC88ttern.png", filename_fix($f));

        $f = "וְאָהַבְתָּ.png";
        $this->assertEquals("D795D6B0D790D6B8D794D6B7D791D6B0D7AAD6BCD6B8.png", filename_fix($f));

        $this->assertEquals("D09AD0BED0B3D0B0D182D0BE.png", filename_fix("Когато.png"));
    }

    function testtag_Status() {

        $c = "";
        $s = tag_Status($c, 'publish');
        $this->assertEquals("", $c);
        $this->assertEquals("publish", $s);

        $c = "status:private";
        $s = tag_Status($c, 'publish');
        $this->assertEquals("", $c);
        $this->assertEquals("private", $s);

        $c = "status: private";
        $s = tag_Status($c, 'publish');
        $this->assertEquals("", $c);
        $this->assertEquals("private", $s);

        $c = "status:blah";
        $s = tag_Status($c, 'publish');
        $this->assertEquals("status:blah", $c);
        $this->assertEquals("publish", $s);

        $c = "multi\nstatus: private\nline";
        $s = tag_Status($c, 'publish');
        $this->assertEquals("multi\n\nline", $c);
        $this->assertEquals("private", $s);
    }

    function testgetPostAuthorDetails() {
        $s = "subject";
        $c = "content";
        $e = new stdClass();
        $e->headers = array();
        $e->headers['date'] = "Jan 1, 2013";
        $e->headers['from'] = "wayne@postieplugin.com";

        $r = getPostAuthorDetails($s, $c, $e);

        $this->assertEquals($s, "subject");
        $this->assertEquals($c, "content");
        $this->assertEquals($r['author'], "wayne");
        $this->assertEquals($r['email'], "wayne@postieplugin.com");
        $this->assertEquals($r['emaildate'], "Jan 1, 2013");
    }

}

?>
