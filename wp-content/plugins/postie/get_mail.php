<?php

//support moving wp-config.php as described here http://codex.wordpress.org/Hardening_WordPress#Securing_wp-config.php
$wp_config_path = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($wp_config_path . DIRECTORY_SEPARATOR . "wp-config.php")) {
    include_once ($wp_config_path . DIRECTORY_SEPARATOR . "wp-config.php");
} else {
    include_once (dirname($wp_config_path)) . DIRECTORY_SEPARATOR . "wp-config.php";
}

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mimedecode.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'postie-functions.php');
if (!function_exists('file_get_html'))
    require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'simple_html_dom.php');

EchoInfo("Starting mail fetch");
EchoInfo("Time: " . date('Y-m-d H:i:s', time()) . " GMT");
include('Revision');
$wp_content_path = dirname(dirname(dirname(__FILE__)));
DebugEcho("wp_content_path: $wp_content_path");
if (file_exists($wp_content_path . DIRECTORY_SEPARATOR . "filterPostie.php")) {
    DebugEcho("found filterPostie.php in wp-content");
    include_once ($wp_content_path . DIRECTORY_SEPARATOR . "filterPostie.php");
}

$test_email = null;
$config = config_Read();
extract($config);
if (!isset($maxemails))
    $maxemails = 0;

$emails = FetchMail($mail_server, $mail_server_port, $mail_userid, $mail_password, $input_protocol, $time_offset, $test_email, $delete_mail_after_processing, $maxemails, $email_tls);
$message = 'Done.';

EchoInfo(sprintf(__("There are %d messages to process", "postie"), count($emails)));

if (function_exists('memory_get_usage'))
    DebugEcho(__("memory at start of e-mail processing:") . memory_get_usage());

DebugEcho("Error log: " . ini_get('error_log'));
DebugDump($config);

//loop through messages
foreach ($emails as $email) {
    DebugEcho("------------------------------------");
    //sanity check to see if there is any info in the message
    if ($email == NULL) {
        $message = __('Dang, message is empty!', 'postie');
        EchoInfo($message);
        continue;
    } else if ($email == 'already read') {
        $message = __("There does not seem to be any new mail.", 'postie');
        EchoInfo($message);
        continue;
    }

    $mimeDecodedEmail = DecodeMIMEMail($email, true);

    DebugEmailOutput($email, $mimeDecodedEmail);

    //Check poster to see if a valid person
    $poster = ValidatePoster($mimeDecodedEmail, $config);
    if (!empty($poster)) {
        PostEmail($poster, $mimeDecodedEmail, $config);
    } else {
        EchoInfo("Ignoring email - not authorized.");
    }
    flush();
}

if (function_exists('memory_get_usage'))
    DebugEcho("memory at end of e-mail processing:" . memory_get_usage());

?>