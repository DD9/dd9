<?php
// try to connect to server with different protocols/ and userids
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "postie-functions.php");
include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . "wp-config.php");
require_once("postie-functions.php");

$config = config_Read();
extract($config);
$title = __("Postie Diagnosis");
$parent_file = 'options-general.php?page=postie/postie.php';
get_currentuserinfo();

if (!current_user_can('manage_options')) {
    LogInfo("non-admin tried to set options");
    echo "<h2> Sorry only admin can run this file</h2>";
    exit();
}
DebugEcho("Error log: " . ini_get('error_log'));
$images = array("Test.png", "Test.jpg", "Test.gif");
?>
<div class="wrap"> 
    <h1>Postie Configuration Test</h1>
    <?php
    if (isMarkdownInstalled()) {
        ?>
        <h1>Warning!</h1>
        <p>You currently have the Markdown plugin installed. It will cause problems if you send in HTML
            email. Please turn it off if you intend to send email using HTML</p>");
        <?php
    }

    if (!isPostieInCorrectDirectory()) {
        EchoInfo("Warning! Postie expects to be in its own directory named postie.");
    } else {
        EchoInfo("Postie is in " . dirname(__FILE__));
    }
    ?>

    <br/>
    <h2>International support</h2>
    <?php
    if (HasIconvInstalled()) {
        EchoInfo("iconv: installed");
    } else {
        EchoInfo("Warning! Postie requires that iconv be enabled.");
    }
    
    if (function_exists('imap_mime_header_decode')) {
        EchoInfo("imap: installed");
    } else {
        EchoInfo("Warning! Postie requires that imap be enabled.");
    }

    if (HasMbStringInstalled()) {
        EchoInfo("mbstring: installed");
    } else {
        EchoInfo("Warning! Postie requires that mbstring be enabled.");
    }
    ?>

    <h2>Clock Tests</h2>
    <p>This shows what time it would be if you posted right now</p>
    <?php
    $content = "";
    $data = filter_Delay($content);
    EchoInfo("GMT: $data[1]");
    EchoInfo("Current: $data[0]");
    ?>

    <h2>Connect to Mail Host</h2>

    <?php
    if (!$mail_server || !$mail_server_port || !$mail_userid) {
        EchoInfo("NO - check server settings");
    } else {
        DebugEcho("checking");
    }
    
    switch (strtolower($config["input_protocol"])) {
        case 'imap':
        case 'imap-ssl':
        case 'pop3-ssl':
            if (!HasIMAPSupport()) {
                EchoInfo("Sorry - you do not have IMAP php module installed - it is required for this mail setting.");
            } else {
                require_once("postieIMAP.php");
                $mail_server = &PostieIMAP::Factory($config["input_protocol"]);
                if (!$mail_server->connect($config["mail_server"], $config["mail_server_port"], $config["mail_userid"], $config["mail_password"])) {
                    EchoInfo("Unable to connect. The server said:");
                    EchoInfo($mail_server->error());
                } else {
                    EchoInfo("Successful " . strtoupper($config['input_protocol']) . " connection on port {$config["mail_server_port"]}");
                    EchoInfo("# of waiting messages: " . $mail_server->getNumberOfMessages());
                }
            }
            break;
        case 'pop3':
        default:
            require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-pop3.php');
            $pop3 = &new POP3();
            if (!$pop3->connect($config["mail_server"], $config["mail_server_port"])) {
                EchoInfo("Unable to connect. The server said:");
                EchoInfo($pop3->ERROR);
            } else {
                EchoInfo("Sucessful " . strtoupper($config['input_protocol']) . " connection on port {$config["mail_server_port"]}");
            }
            break;
    }
    ?>
</div>
