<?php

require_once '../mimedecode.php';

class postiefunctions2Test extends PHPUnit_Framework_TestCase {

    function process_file($test_file, $config) {

        $message = file_get_contents($test_file);
        $email = unserialize($message);

        $isreply = false;
        $mimeDecodedEmail = DecodeMIMEMail($email);
        $pm = new PostiePostModifiers();
        $post = CreatePost('wayne', $mimeDecodedEmail, 1, $isreply, $config, $pm);

        return $post;
    }

    function testSimpleHtmlDomWithWhitespace() {
        $html = str_get_html("\n", true, true, DEFAULT_TARGET_CHARSET, false);
        $r = $html->save();
        $this->assertEquals("\n", $r);

        $html = str_get_html("<div>\n<p>some text</p>\n</div>\n", true, true, DEFAULT_TARGET_CHARSET, false);
        $r = $html->save();
        $this->assertEquals("<div>\n<p>some text</p>\n</div>\n", $r);
    }

    function testBase64Subject() {
        $message = file_get_contents("data/b-encoded-subject.var");
        $email = unserialize($message);
        $decoded = DecodeMIMEMail($email, true);
        $this->assertEquals("テストですよ", $decoded->headers['subject']);
    }

    function testQuotedPrintableSubject() {
        $message = file_get_contents("data/q-encoded-subject.var");
        $email = unserialize($message);
        $decoded = DecodeMIMEMail($email, true);
        $this->assertEquals("Pár minut před desátou a jsem v práci první", $decoded->headers['subject']);
    }

    function testInlineImage() {

        $config = config_GetDefaults();
        $config['prefer_text_type'] = 'html';
        $config['imagetemplate'] = '<a href="{FILELINK}">{FILENAME}</a>';

        $post = $this->process_file("data/inline.var", $config);
        $this->assertEquals('test<div><br></div><div><img src="http://example.net/wp-content/uploads/filename" alt="Inline image 1"><br></div><div><br></div><div>test</div>   ', $post['post_content']);
        $this->assertEquals('inline', $post['post_title']);
    }

    function testLineBreaks() {

        $config = config_GetDefaults();
        $config['convertnewline'] = true;

        $post = $this->process_file("data/linebreaks.var", $config);
        $this->assertEquals("Test<br />\n<br />\nEen stuck TekstEen stuck TekstEen stuck TekstEen stuck Tekst<br />\n<br />\nEen stuck TekstEen stuck Tekst<br />\n<br />\n<br />\nEen stuck TekstEen stuck Tekst<br />\n", $post['post_content']);
    }

    function testjapaneseAttachment() {

        $config = config_GetDefaults();

        $post = $this->process_file("data/japanese-attachment.var", $config);
        $this->assertEquals('JP?B?UG9zdGllGyRCTVElRiU5JUglYSE8JWsbKEo=?=', $post['post_title']);
        //$this->assertEquals('', $post['post_content']);
    }

    function testIcsAttachement() {

        $config = config_GetDefaults();
        $config['prefer_text_type'] = 'html';

        $post = $this->process_file("data/ics-attachment.var", $config);
        $this->assertEquals("<div dir='ltr'>sample text<div><br></div></div>   <a href='http://example.net/wp-content/uploads/filename'><img src='localhost/postie/icons/silver/default-32.png' alt='default icon' />sample.ics</a> ", $post['post_content']);
    }

    function testTagsImg() {
        echo "testTagsImg";
        $config = config_GetDefaults();
        $config['start_image_count_at_zero'] = true;
        $config['imagetemplate'] = '<a href="{FILELINK}">{FILENAME}</a>';

        $post = $this->process_file("data/only-tags-img.var", $config);
        $this->assertEquals('tags test', $post['post_title']);
        $this->assertEquals(2, count($post['tags_input']));
        $this->assertEquals('test', $post['tags_input'][0]);
        $this->assertEquals('tag2', $post['tags_input'][1]);
        $this->assertEquals(' <a href="http://example.net/wp-content/uploads/filename">close_account.png</a><br />  ', $post['post_content']);
    }

    function testSig() {
        echo "testSig";
        $config = config_GetDefaults();
        $config['prefer_text_type'] = 'html';

        $post = $this->process_file("data/signature.var", $config);
        $this->assertEquals('test content<div><br></div>   ', $post['post_content']);

        $config['prefer_text_type'] = 'html';
        $post = $this->process_file("data/signature.var", $config);
        $this->assertEquals('test content<div><br></div>   ', $post['post_content']);
    }

    function testQuotedPrintable() {
        $str = quoted_printable_decode("ABC=C3=C4=CEABC=");
        $str = iconv('ISO-8859-7', 'UTF-8', $str);
        $this->assertEquals("ABCΓΔΞABC", $str);

        $str = quoted_printable_decode('<span style=3D"font-family:arial,sans-serif;font-size:13px">ABC=C3=C4=CEABC=</span><br>');
        $str = iconv('ISO-8859-7', 'UTF-8', $str);
        $this->assertEquals('<span style="font-family:arial,sans-serif;font-size:13px">ABCΓΔΞABC=</span><br>', $str);
    }

    function testBase64() {
        $str = base64_decode("QUJDw8TOQUJDCg==");
        $str = iconv('ISO-8859-7', 'UTF-8', $str);
        $this->assertEquals("ABCΓΔΞABC\n", $str);
    }

    function testHandleMessageEncoding() {
        $e = HandleMessageEncoding('quoted-printable', 'iso-8859-7', '<span style=3D"font-family:arial,sans-serif;font-size:13px">ABC=C3=C4=CEABC=</span><br>');
        $this->assertEquals('<span style="font-family:arial,sans-serif;font-size:13px">ABCΓΔΞABC=</span><br>', $e);
    }

    function testGreek() {
        $config = config_GetDefaults();
        $message = file_get_contents("data/greek.var");
        $email = unserialize($message);

        $decoded = DecodeMIMEMail($email);
        print_r($decoded);

        filter_PreferedText($decoded, 'html');
        $attachments = array(
            "html" => array(), //holds the html for each image
            "cids" => array(), //holds the cids for HTML email
            "image_files" => array() //holds the files for each image
        );
        $content = GetContent($decoded, $attachments, 1, 'wayne@devzing.com', $config);
        print_r($content);
    }

    public function testReplaceImagePlaceHolders() {
        $c = "";
        $config = config_GetDefaults();
        $config['allow_html_in_body'] = true;

        $attachements = array("image.jpg" => '<img title="{CAPTION}" />');

        filter_ReplaceImagePlaceHolders($c, array(), $config);
        $this->assertEquals("", $c);

        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('<img title="" />', $c);

        $c = "#img1#";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('<img title="" />', $c);

        $c = "test #img1# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="" /> test', $c);

        $c = "test #img1 caption='1'# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="1" /> test', $c);

        $c = "test #img1 caption=# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="" /> test', $c);

        $c = "test #img1 caption=1# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="1" /> test', $c);

        $c = "test #img1 caption='! @ % ^ & * ( ) ~ \"Test\"'# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="! @ % ^ &amp; * ( ) ~ &quot;Test&quot;" /> test', $c);

        $c = "test <div>#img1 caption=&#39;! @ % ^ &amp; * ( ) ~ &quot;Test&quot;&#39;#</div> test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <div><img title="&amp;" />39;! @ % ^ &amp; * ( ) ~ &quot;Test&quot;&#39;#</div> test', $c);

        $c = "test #img1 caption=\"I'd like some cheese.\"# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="I&#039;d like some cheese." /> test', $c);

        $c = "test #img1 caption=\"Eiskernbrecher mögens laut\"# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="Eiskernbrecher mögens laut" /> test', $c);

        $c = "test #img1 caption='[image-caption]'# test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="[image-caption]" /> test', $c);

        $c = "test #img1 caption='1'# test #img2 caption='2'#";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="1" /> test #img2 caption=\'2\'#', $c);

        $attachements = array("image1.jpg" => 'template with {CAPTION}', "image2.jpg" => 'template with {CAPTION}');
        $c = "test #img1 caption='1'# test #img2 caption='2'#";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("test template with 1 test template with 2", $c);

        $config['auto_gallery'] = true;
        $config['images_append'] = false;
        $c = "test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("[gallery]\ntest", $c);

        $config['images_append'] = true;
        $c = "test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("test\n[gallery]", $c);

        $config['images_append'] = true;
        $c = "test";
        filter_ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("test\n[gallery]", $c);

        $c = "test";
        filter_ReplaceImagePlaceHolders($c, array(), $config);
        $this->assertEquals("test", $c);
    }

}

?>
