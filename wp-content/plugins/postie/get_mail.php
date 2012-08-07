<?php

//Load up some usefull libraries
include_once (dirname(dirname(dirname(dirname(__FILE__)))) .  DIRECTORY_SEPARATOR."wp-config.php");
require_once (dirname(__FILE__). DIRECTORY_SEPARATOR . 'mimedecode.php');
require_once (dirname(__FILE__). DIRECTORY_SEPARATOR . 'postie-functions.php');
	

/* END OF USER VARIABLES */
//some variables
//error_reporting(2037);

//Retreive emails 
print("<pre>\n");
print("This is the postie plugin\n");
print("time:" . time() . "\n");
include('Revision');
$config = get_option('postie-settings');
extract($config);
$emails = FetchMail($mail_server, $mail_server_port, $mail_userid, 
    $mail_password, $input_protocol, $time_offset, $test_email,
    $delete_mail_after_processing);
//loop through messages
foreach ($emails as $email) {
  if (function_exists('memory_get_usage'))
    echo "memory at start of e-mail processing:" . memory_get_usage() . "\n";
    //sanity check to see if there is any info in the message
    if ($email == NULL ) { 
      $message= __('Dang, message is empty!', 'postie'); 
      continue; 
    } else if ($email=='already read') {
      $message = "\n" . __("There does not seem to be any new mail.", 'postie') .
      "\n";
      continue;
    }
    $message='';
    $mimeDecodedEmail = DecodeMIMEMail($email, true);
    $from = RemoveExtraCharactersInEmailAddress(trim($mimeDecodedEmail->headers["from"]));
    /*
    if ($from != "") {
        continue;
    }
    */

    //Check poster to see if a valid person
    $poster = ValidatePoster($mimeDecodedEmail, $config);
    if (!empty($poster)) {
        if ($test_email) 
          DebugEmailOutput($email,$mimeDecodedEmail); 
        PostEmail($poster,$mimeDecodedEmail, $config);
    }
    else {
        print("<p>Ignoring email - not authorized.\n");
    }
  if (function_exists('memory_get_usage'))
    echo "memory at end of e-mail processing:" . memory_get_usage() . "\n";
} // end looping over messages
print $message;
print("</pre>\n");
    
/* END PROGRAM */

// end of script
?>