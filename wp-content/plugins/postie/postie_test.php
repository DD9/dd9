<?php
// try to connect to server with different protocols/ and userids
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'postie-wp-config.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "postie-functions.php");
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
?>
<div class="wrap"> 
    <h1>Postie Configuration Test</h1>
    <?php
    postie_environment();
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
        EchoInfo("FAIL - server settings not complete");
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
                if ($email_tls) {
                    $mail_server->TLSOn();
                }
                if (!$mail_server->connect($config["mail_server"], $config["mail_server_port"], $config["mail_userid"], $config["mail_password"])) {
                    EchoInfo("Unable to connect. The server said:");
                    EchoInfo($mail_server->error());
                } else {
                    EchoInfo("Successful " . strtoupper($config['input_protocol']) . " connection on port {$config["mail_server_port"]}");
                    EchoInfo("# of waiting messages: " . $mail_server->getNumberOfMessages());
                    $mail_server->disconnect();
                }
            }
            break;
        case 'pop3':
        default:
            require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-pop3.php');
            $pop3 = new POP3();
            if (defined('POSTIE_DEBUG')) {
                $pop3->DEBUG = POSTIE_DEBUG;
            }
            if (!$pop3->connect($config["mail_server"], $config["mail_server_port"])) {
                EchoInfo("Unable to connect. The server said:" . $pop3->ERROR);
            } else {
                EchoInfo("Sucessful " . strtoupper($config['input_protocol']) . " connection on port {$config["mail_server_port"]}");
                $msgs = $pop3->login($config["mail_userid"], $config["mail_password"]);
                if ($msgs === false) {
                    //workaround for bug reported here Apr 12, 2013
                    //https://sourceforge.net/tracker/?func=detail&atid=100311&aid=3610701&group_id=311
                    //originally repoted here:
                    //https://core.trac.wordpress.org/ticket/10587
                    if (empty($pop3->ERROR))
                        EchoInfo("No waiting messages");
                    else
                        EchoInfo("Unable to login. The server said:" . $pop3->ERROR);
                } else {
                    EchoInfo("# of waiting messages: $msgs");
                }
                $pop3->quit();
            }
            break;
    }
    ?>
</div>
