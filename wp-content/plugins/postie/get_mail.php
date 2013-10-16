<?php

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'postie-wp-config.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mimedecode.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'postie-functions.php');
if (!function_exists('file_get_html'))
    require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'simple_html_dom.php');

EchoInfo("Starting mail fetch");
postie_environment();
$wp_content_path = dirname(dirname(dirname(__FILE__)));
DebugEcho("wp_content_path: $wp_content_path");
if (file_exists($wp_content_path . DIRECTORY_SEPARATOR . "filterPostie.php")) {
    DebugEcho("found filterPostie.php in $wp_content_path");
    include_once ($wp_content_path . DIRECTORY_SEPARATOR . "filterPostie.php");
}

if (has_filter('postie_post')){
    echo "Postie: filter 'postie_post' is depricated in favor of 'postie_post_before'";
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

DebugDump($config);

//loop through messages
$message_number = 0;
foreach ($emails as $email) {
    $message_number++;
    DebugEcho("$message_number: ------------------------------------");
    //sanity check to see if there is any info in the message
    if ($email == NULL) {
        $message = __('Dang, message is empty!', 'postie');
        EchoInfo("$message_number: $message");
        continue;
    } else if ($email == 'already read') {
        $message = __("Message is already marked 'read'.", 'postie');
        EchoInfo("$message_number: $message");
        continue;
    }

    $mimeDecodedEmail = DecodeMIMEMail($email);

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
EchoInfo("Mail fetch complete, $message_number emails");

if (function_exists('memory_get_usage'))
    DebugEcho("memory at end of e-mail processing:" . memory_get_usage());
?>