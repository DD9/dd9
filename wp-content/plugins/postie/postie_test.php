<?php
// try to connect to server with different protocols/ and userids
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR ."postie-functions.php");
include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR. "wp-config.php");
//require_once('admin.php');
require_once("postie-functions.php");
$config = get_postie_config();
extract($config);
$title = __("Postie Diagnosis");
$parent_file = 'options-general.php?page=postie/postie.php';
get_currentuserinfo();
?>
<?php 
  if (!current_user_can('manage_options')) {
    echo "<h2> Sorry only admin can run this file</h2>";
    exit();
  }
?>

<?
    $images = array("Test.png",
                    "Test.jpg",
                    "Test.gif");
?>
<div class="wrap"> 
    <h1>Postie Configuration Test</h1>
    <?php
        if (TestForMarkdown()) {
            print("<h1>Warning!</h1>
                    <p>You currently have the Markdown plugin installed. It will cause problems if you send in HTML
                    email. Please turn it off if you intend to send email using HTML</p>");

        }
    ?>
    <?php 
        
        if (!TestPostieDirectory()) {
            print("<h1>Warning!</h1>
                    <p>Postie expects to be in its own directory named postie.</p>");
        }
        else  {
            print("<p>Postie is in ".dirname(__FILE__)."</p>");
        }
         ?>

    <br/>
    <h2>International support<h2>
    <p><i><?php _e('Only required for international character set support',
    'postie') ?></i>
   <table>
   <tr>
   <th>iconv</th>
    <td> <?php if (HasIconvInstalled())  _e('yes', 'postie');  ?></td>
    </tr>
    <tr>
   <th>imap <small>(required for subjects)</small></th>
    <td> <?php if (function_exists('imap_mime_header_decode')) _e('yes', 'postie') ; ?></td>
    </tr>
    </table>

    </p>
    <br/>
    <h2>Clock Tests<h2>
    <p>This shows what time it would be if you posted right now</p>
    <?php
     $content ="";
     $data = DeterminePostDate($content);

    ?>
    <p><?php print("GMT:". $data[1]);?></p>
    <p><?php print("Current:". $data[0]);?></p>
    <h2>Mail Tests</h2>
    <p>These try to confirm that the email configuration is correct.</p>

    <table>
    <tr>
        <th>Test</th>
        <th>Result</th>
    </tr>
    <tr>
        <th>Connect to Mail Host</th>
        <td>
           <?php
            if (!$mail_server || !$mail_server_port || !$mail_userid) {
              print("NO - check server settings");
            }
                switch( strtolower($config["input_protocol"]) ) {
                    case 'imap':
                    case 'imap-ssl':
                    case 'pop3-ssl':
                        if (!HasIMAPSupport()) {
                            print("Sorry - you do not have IMAP php module installed - it is required for this mail setting.");
                        }
                        else {
                            require_once("postieIMAP.php");
                            $mail_server = &PostieIMAP::Factory($config["input_protocol"]);
                            if (!$mail_server->connect($config["mail_server"], $config["mail_server_port"],$config["mail_userid"],$config["mail_password"])) {
                                print("Unable to connect. The server said - ".$mail_server->error());
                                print("<br/>Try putting in your full email address as a userid and try again.");
                            }
                            else {
                                print("Yes");
                            }
                        }
                        break;
                    case 'pop3':
                    default: 
                        require_once(ABSPATH.WPINC.DIRECTORY_SEPARATOR.'class-pop3.php');
                        $pop3 = &new POP3();
                        if (!$pop3->connect($config["mail_server"], $config["mail_server_port"])) {
                                print("Unable to connect. The server said - ".$pop3->ERROR);
                                print("<br/>Try putting in your full email address as a userid and try again.");
                        }
                        else {
                            print("Yes");
                        }
                        break;

                }
           ?>
            </td>
    </tr>


    </table>
</div>
