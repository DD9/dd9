<?php

require '../mimedecode.php';

class postiefunctions2Test extends PHPUnit_Framework_TestCase {

    function standardConfig() {
        return array(
            'prefer_text_type' => 'plain',
            'allow_html_in_body' => false,
            'banned_files_list' => array(),
            'imagetemplate' => '<a href="{FILELINK}">{FILENAME}</a>',
            'drop_signature' => true,
            'message_encoding' => 'UTF-8',
            'message_dequote' => true,
            'allow_html_in_subject' => true,
            'message_start' => ':start',
            'message_end' => ':end',
            'sig_pattern_list' => array('--', '- --'),
            'custom_image_field' => false,
            'start_image_count_at_zero' => false,
            'images_append' => false,
            'filternewlines' => true,
            'convertnewline' => false,
            'auto_gallery' => false,
            'image_placeholder' => '#img%#'
        );
    }

    function testInlineImage() {

        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );

        $message = file_get_contents("data/inline.var");
        $email = unserialize($message);
        $decoded = DecodeMIMEMail($email);

        $partcnt = count($decoded->parts);
        $this->assertEquals(2, $partcnt);

        FilterTextParts($decoded, "plain");

        $attachments = array(
            "html" => array(), //holds the html for each image
            "cids" => array(), //holds the cids for HTML email
            "image_files" => array() //holds the files for each image
        );

        $config = $this->standardConfig();
        $content = GetContent($decoded, $attachments, 1, "wayne", $config);
    }

    function testMultipleImagesWithSig() {

        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );


        $message = file_get_contents("data/multiple images with signature.var");
        $email = unserialize($message);
        $decoded = DecodeMIMEMail($email);

        $partcnt = count($decoded->parts);
        $this->assertEquals(3, $partcnt);

        FilterTextParts($decoded, "plain");

        $attachments = array(
            "html" => array(), //holds the html for each image
            "cids" => array(), //holds the cids for HTML email
            "image_files" => array() //holds the files for each image
        );

        $config = $this->standardConfig();
        $content = GetContent($decoded, $attachments, 1, "wayne", $config);
    }

    function testSig() {

        $message = file_get_contents("data/signature.var");
        $email = unserialize($message);
        $decoded = DecodeMIMEMail($email);

        $partcnt = count($decoded->parts);
        $this->assertEquals(2, $partcnt);

        FilterTextParts($decoded, "plain");

        $attachments = array(
            "html" => array(), //holds the html for each image
            "cids" => array(), //holds the cids for HTML email
            "image_files" => array() //holds the files for each image
        );

        $config = $this->standardConfig();
        $filternewlines = $config['filternewlines'];
        $convertnewline = $config['convertnewline'];

        $content = GetContent($decoded, $attachments, 1, "wayne", $config);

        $subject = GetSubject($decoded, $content, $config);
        $this->assertEquals('signature', $subject);

        $customImages = SpecialMessageParsing($content, $attachments, $config);
        $this->assertEquals(null, $customImages);
        $this->assertEquals("<div>test content\n\n", $content);

        $post_excerpt = GetPostExcerpt($content, $filternewlines, $convertnewline);

        $postAuthorDetails = getPostAuthorDetails($subject, $content, $decoded);
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
        $config = $this->standardConfig();
        $message = file_get_contents("data/greek.var");
        $email = unserialize($message);

        $decoded = DecodeMIMEMail($email);
        print_r($decoded);

        FilterTextParts($decoded, 'html');
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
        $config = $this->standardConfig();
        $attachements = array("image.jpg" => '<img title="{CAPTION}" />');

        ReplaceImagePlaceHolders($c, array(), $config);
        $this->assertEquals("", $c);

        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('<img title="" />', $c);

        $c = "#img1#";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('<img title="" />', $c);

        $c = "test #img1# test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="" /> test', $c);

        $c = "test #img1 caption='1'# test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="1" /> test', $c);

        $c = "test #img1 caption='! @ % ^ & * ( ) ~ \"Test\"'# test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="! @ % ^ &amp; * ( ) ~ &quot;Test&quot;" /> test', $c);

        $c = "test <div>#img1 caption=&#39;! @ % ^ &amp; * ( ) ~ &quot;Test&quot;&#39;#</div> test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("test <div><img title=\"! @ % ^ &amp; * ( ) ~ &quot;Test&quot;\" /></div> test", $c);

        $c = "test #img1 caption=\"I'd like some cheese.\"# test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="I&#039;d like some cheese." /> test', $c);

        $c = "test #img1 caption=\"Eiskernbrecher mögens laut\"# test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="Eiskernbrecher mögens laut" /> test', $c);

        $c = "test #img1 caption='[image-caption]'# test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="[image-caption]" /> test', $c);

        $c = "test #img1 caption='1'# test #img2 caption='2'#";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals('test <img title="1" /> test #img2 caption=\'2\'#', $c);

        $attachements = array("image1.jpg" => 'template with {CAPTION}', "image2.jpg" => 'template with {CAPTION}');
        $c = "test #img1 caption='1'# test #img2 caption='2'#";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("test template with 1 test template with 2", $c);

        $config['auto_gallery'] = true;
        $c = "test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("[gallery]\ntest", $c);

        $config['images_append'] = true;
        $c = "test";
        ReplaceImagePlaceHolders($c, $attachements, $config);
        $this->assertEquals("test[gallery]", $c);

        $c = "test";
        ReplaceImagePlaceHolders($c, array(), $config);
        $this->assertEquals("test", $c);
    }

}

?>
