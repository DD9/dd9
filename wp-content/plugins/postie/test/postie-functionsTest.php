<?php

require 'wpstub.php';
require'../postie-functions.php';
require'../simple_html_dom.php';
require '../postie.php';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('POSTIE_DEBUG', true);

class postiefunctionsTest extends PHPUnit_Framework_TestCase {

    public function testAllowCommentsOnPost() {
        $original_content = "test content, no comment control";
        $modified_content = "test content, no comment control";
        $allow = AllowCommentsOnPost($modified_content);
        $this->assertEquals("open", $allow);
        $this->assertEquals($original_content, $modified_content);

        $original_content = "test content, comment control closed ";
        $modified_content = "test content, comment control closed comments:0";
        $allow = AllowCommentsOnPost($modified_content);
        $this->assertEquals("closed", $allow);
        $this->assertEquals($original_content, $modified_content);

        $original_content = "test content, comment control open ";
        $modified_content = "test content, comment control open comments:1";
        $allow = AllowCommentsOnPost($modified_content);
        $this->assertEquals("open", $allow);
        $this->assertEquals($original_content, $modified_content);

        $original_content = "test content, comment control registered only ";
        $modified_content = "test content, comment control registered only comments:2";
        $allow = AllowCommentsOnPost($modified_content);
        $this->assertEquals("registered_only", $allow);
        $this->assertEquals($original_content, $modified_content);
    }

    public function testBannedFileName() {
        $this->assertFalse(BannedFileName("", null));
        $this->assertFalse(BannedFileName("", ""));
        $this->assertFalse(BannedFileName("", array()));
        $this->assertFalse(BannedFileName("test", array()));
        $this->assertTrue(BannedFileName("test", array("test")));
        $this->assertFalse(BannedFileName("test", array("test1")));
        $this->assertTrue(BannedFileName("test.exe", array("*.exe")));
        $this->assertFalse(BannedFileName("test.pdf", array("*.exe")));
        $this->assertFalse(BannedFileName("test.pdf", array("*.exe", "*.js", "*.cmd")));
        $this->assertFalse(BannedFileName("test.cmd.pdf", array("*.exe", "*.js", "*.cmd")));
        $this->assertTrue(BannedFileName("test test.exe", array("*.exe")));
    }

    public function testCheckEmailAddress() {
        $this->assertFalse(CheckEmailAddress(null, null));
        $this->assertFalse(CheckEmailAddress(null, array()));
        $this->assertFalse(CheckEmailAddress("", array()));
        $this->assertFalse(CheckEmailAddress("", array("")));
        $this->assertFalse(CheckEmailAddress("bob", array("jane")));
        $this->assertTrue(CheckEmailAddress("bob", array("bob")));
        $this->assertTrue(CheckEmailAddress("bob", array("BoB")));
        $this->assertTrue(CheckEmailAddress("bob", array("bob", "jane")));
        $this->assertTrue(CheckEmailAddress("bob", array("jane", "bob")));
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

    public function testDeterminePostDate() {
        $content = "test";
        $r = DeterminePostDate($content);
        $this->assertTrue(is_array($r));
        $this->assertEquals(3, count($r));
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test", $content);

        $content = "test delay:";
        $r = DeterminePostDate($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test delay:", $content);

        $content = "test delay:1h";
        $r = DeterminePostDate($content);
        $this->assertEquals(3600, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:1d";
        $r = DeterminePostDate($content);
        $this->assertEquals(86400, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:1m";
        $r = DeterminePostDate($content);
        $this->assertEquals(60, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:m";
        $r = DeterminePostDate($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:dhm";
        $r = DeterminePostDate($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:x";
        $r = DeterminePostDate($content);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test delay:x", $content);

        $content = "test delay:-1m";
        $r = DeterminePostDate($content);
        $this->assertEquals(-60, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:1d1h1m";
        $r = DeterminePostDate($content);
        $this->assertEquals(90060, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test delay:d1hm";
        $r = DeterminePostDate($content);
        $this->assertEquals(3600, $r[2]);
        $this->assertEquals("test ", $content);

        $content = "test";
        $r = DeterminePostDate($content, '2012-11-20 08:00', 1);
        $this->assertEquals('2012-11-20 17:00:00', $r[0]);
        $this->assertEquals(0, $r[2]);
        $this->assertEquals("test", $content);
    }

    public function testEndFilter() {
        $c = "test";
        $this->assertEquals("test", EndFilter($c, "xxx"));

        $c = "test xxx";
        $this->assertEquals("test ", EndFilter($c, "xxx"));

        $c = "test xxx test";
        $this->assertEquals("test ", EndFilter($c, "xxx"));

        $c = "tags: Station, Kohnen, Flugzeug\n:end\n21.10.2012";
        $this->assertEquals("tags: Station, Kohnen, Flugzeug\n", EndFilter($c, ":end"));
        
        $c = "This is a test :end";
        $this->assertEquals("This is a test ", EndFilter($c, ":end"));
        
    }

    public function testFilterNewLines() {
        $c = "test";
        $this->assertEquals("test", FilterNewLines($c));
        $this->assertEquals("test", FilterNewLines($c, true));

        $c = "test\n";
        $this->assertEquals("test ", FilterNewLines($c));
        $this->assertEquals("test<br />\n", FilterNewLines($c, true));

        $c = "test\r\n";
        $this->assertEquals("test ", FilterNewLines($c));
        $this->assertEquals("test<br />\n", FilterNewLines($c, true));

        $c = "test\r";
        $this->assertEquals("test ", FilterNewLines($c));
        $this->assertEquals("test<br />\n", FilterNewLines($c, true));

        $c = "test\n\n";
        $this->assertEquals("test ", FilterNewLines($c));
        $this->assertEquals("test<br />\n", FilterNewLines($c, true));

        $c = "test\r\n\r\n";
        $this->assertEquals("test ", FilterNewLines($c));
        $this->assertEquals("test<br />\n", FilterNewLines($c, true));

        $c = "test\r\n\r\ntest\n\ntest\rtest\r\ntest\ntest";
        $this->assertEquals("test test test test test test", FilterNewLines($c));
        $this->assertEquals("test<br />\ntest<br />\ntest<br />\ntest<br />\ntest<br />\ntest", FilterNewLines($c, true));
    }

    public function testGetNameFromEmail() {
        $this->assertEquals("", GetNameFromEmail(""));
        $this->assertEquals("Wayne", GetNameFromEmail('Wayne <wayne@devzing.com>'));
        $this->assertEquals("wayne", GetNameFromEmail('wayne@devzing.com'));
    }

    public function testGetPostType() {
        $subject = "test";
        $this->assertEquals("post", GetPostType($subject));
        $this->assertEquals("test", $subject);

        $subject = "custom//test";
        $this->assertEquals("custom", GetPostType($subject));
        $this->assertEquals("test", $subject);

        $subject = "//test";
        $this->assertEquals("post", GetPostType($subject));
        $this->assertEquals("test", $subject);

        $subject = "//";
        $this->assertEquals("post", GetPostType($subject));
        $this->assertEquals("", $subject);

        $subject = "Image//test";
        $this->assertEquals("image", GetPostType($subject));
        $this->assertEquals("test", $subject);

        $subject = "Image // test";
        $this->assertEquals("image", GetPostType($subject));
        $this->assertEquals("test", $subject);

        $subject = "video//test";
        $this->assertEquals("video", GetPostType($subject));
        $this->assertEquals("test", $subject);
    }

    public function testGetPostExcerpt() {
        $c = "test";
        $this->assertEquals("", GetPostExcerpt($c, false, false));

        $c = ":excerptstart test :excerptend test";
        $this->assertEquals("test ", GetPostExcerpt($c, false, false));

        $c = ":excerptstart test";
        $this->assertEquals("", GetPostExcerpt($c, false, false));

        $c = "test :excerptend test";
        $this->assertEquals("", GetPostExcerpt($c, false, false));
    }

    public function testGetPostCategories() {
        global $wpdb;

        $s = "test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("test", $s);

        $s = ":test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("default", $c[0]);
        $this->assertEquals(":test", $s);

        $wpdb->t_get_var = "1";
        $s = "1: test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("1", $c[0]);
        $this->assertEquals("test", $s);

        $wpdb->t_get_var = null;
        $s = "not a category: test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("not a category: test", $s);

        $s = "[not a category] test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("[not a category] test", $s);

        $s = "-not a category- test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("-not a category- test", $s);

        $wpdb->t_get_var = "general";
        $s = "general: test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("general", $c[0]);
        $this->assertEquals("test", $s);

        $s = "[general] test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("general", $c[0]);
        $this->assertEquals("test", $s);


        $s = "-general- test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("general", $c[0]);
        $this->assertEquals("test", $s);

        $wpdb->t_get_var = "";
        $s = "specific: test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals("default", $c[0]);
        $this->assertEquals("specific: test", $s);

        $wpdb->t_get_var = "1";
        $s = "[1] [1] test";
        $c = GetPostCategories($s, "default");
        $this->assertEquals(2, count($c));
        $this->assertEquals("1", $c[0]);
        $this->assertEquals("1", $c[1]);
        $this->assertEquals("test", $s);
    }

    public function testHTML2HTML() {
        $this->assertEquals("", HTML2HTML(""));
        $this->assertEquals("test", HTML2HTML("test"));
        $this->assertEquals("<div>test</div>\n", HTML2HTML("<html lang='en'><body>test</body></html>"));
        $this->assertEquals("<div>test</div>\n", HTML2HTML("<html lang='en'><head><title>title</title></head><body>test</body></html>"));
        $this->assertEquals("<div>test</div>\n", HTML2HTML("<body>test</body>"));
        $this->assertEquals("<strong>test</strong>", HTML2HTML("<strong>test</strong>"));
    }

    public function testSafeFileName() {
        $this->assertEquals("testtest", SafeFileName('test\/:*?"<>|test'));
    }

    public function testremove_signature() {
        $this->assertEquals("", remove_signature("", array()));
        $this->assertEquals("test", remove_signature("test", array()));
        $this->assertEquals("\n", remove_signature("", array("--", "- --")));
        $this->assertEquals("test\n", remove_signature("test", array("--", "- --")));
        $this->assertEquals("line 1\nline 2\n", remove_signature("line 1\nline 2\n--\nsig line 1\nsig line 2", array("--", "- --")));
        $this->assertEquals("line 1\nline 2\n", remove_signature("line 1\nline 2\n- --\nsig line 1\nsig line 2", array("--", "- --")));
        $this->assertEquals("line 1\nline 2\n", remove_signature("line 1\nline 2\n-- \nsig line 1\nsig line 2", array("--", "- --")));
        $this->assertEquals("line 1\nline 2\n", remove_signature("line 1\nline 2\n --\nsig line 1\nsig line 2", array("--", "- --")));
        $this->assertEquals("line 1\nline 2\n", remove_signature("line 1\nline 2\n--", array("--", "- --")));
    }

    public function testmore_reccurences() {
        $sched = array();
        $newsched = postie_more_reccurences($sched);
        $this->assertEquals(3, count($newsched));
    }

    public function testpostie_get_tags() {
        $c = "";
        $t = postie_get_tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("", $c);

        $c = "test";
        $t = postie_get_tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("test", $c);
        
         $c = "test";
        $t = postie_get_tags($c, array("tag1"));
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test", $c);

        $c = "test tags:";
        $t = postie_get_tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("test tags:", $c);

        $c = "test tags:\n";
        $t = postie_get_tags($c, "");
        $this->assertEquals(0, count($t));
        $this->assertEquals("test tags:\n", $c);

        $c = "test tags: tag1";
        $t = postie_get_tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("test ", $c);
        
        $c = "test\ntags: tag1";
        $t = postie_get_tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("test\n", $c);

        $c = "test tags: tag1\n";
        $t = postie_get_tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test \n", $c);

        $c = "test tags:tag1";
        $t = postie_get_tags($c, "");
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test ", $c);
        
         $c = "test tags:tag1";
        $t = postie_get_tags($c, array("tagx"));
        $this->assertEquals(1, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("test ", $c);
        
        $c = "test tags:tag1,tag2";
        $t = postie_get_tags($c, "");
        $this->assertEquals(2, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("tag2", $t[1]);
        $this->assertEquals("test ", $c);
        
        $c = "test tags: tag3,tag4\nmore stuff\n:end";
        $t = postie_get_tags($c, "");
        $this->assertEquals(2, count($t));
        $this->assertEquals("tag3", $t[0]);
        $this->assertEquals("tag4", $t[1]);
        $this->assertEquals("test \nmore stuff\n:end", $c);
        
        $c = "test tags:tag1,tag2\nmore stuff\n:end";
        $t = postie_get_tags($c, "");
        $this->assertEquals(2, count($t));
        $this->assertEquals("tag1", $t[0]);
        $this->assertEquals("tag2", $t[1]);
        $this->assertEquals("test \nmore stuff\n:end", $c);
    }

}

?>
