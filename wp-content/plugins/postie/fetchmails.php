<?php
//if ($_POST['fetchmails']) {
  include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-config.php");
  require_once (dirname(__FILE__). DIRECTORY_SEPARATOR . '../postie/mimedecode.php');
  require_once (dirname(__FILE__). DIRECTORY_SEPARATOR . '../postie/postie-functions.php');
  init();
  fetch_mails();
  exit;
//}
function init() {
/* Sets up database table if it doesn't already exist */
  global $wpdb, $aandcpostie_version;
  $table_name=$wpdb->prefix . 'postie_addresses';
  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    echo "creating table\n";
    $sql = "CREATE TABLE " . $table_name . " (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        server text NOT NULL,
        port smallint(4) DEFAULT '110' NOT NULL,
        email text NOT NULL,
        passwd VARCHAR(64) NOT NULL,
        protocol text NOT NULL,
        offset text NOT NULL,
        category mediumint(9) NOT NULL,
        UNIQUE KEY id (id)
      );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
    $addresses = array(
                 0=> array(
                     'server'=>'yourserver.com',
                     'port' =>'993',
                     'email' => 'youruser',
                     'passwd' => 'yourpword',
                     'protocol' => 'imap-ssl',
                     'offset' => '-5',
                     'category' => 5
                     )
#                 1=> array(
#                     'server'=>'another.com',
#                     'port' =>'993',
#                     'email' => 'anotheruser',
#                     'passwd' => 'anotherpasswd',
#                     'protocol' => 'imap-ssl',
#                     'offset' => '-5'
#                     ) 
                 );
  insert_new_addresses($table_name, $addresses);
}
function insert_new_addresses($table_name, $addresses) {
  /* insert addresses into table */
  global $wpdb;
  $fetch_query = 'SELECT email, server FROM ' .  $table_name;
  $existingAddresses=$wpdb->get_results($fetch_query);
  $existingArray=array();
  foreach ($existingAddresses as $existAdd) {
    array_push($existingArray, $existAdd->email . '@' . $existAdd->server); 
  }
  foreach ($addresses as $address) {
    extract($address);
    $emailAddress = "$email@$server";
    if (!in_array($emailAddress, $existingArray)) {
      $query = "INSERT INTO " . $table_name .
          " (server, port, email, passwd, protocol, offset, category) " .
          "VALUES ('$server', $port, '$email', '$passwd', '$protocol', '$offset', '$category')";
    } else {
      echo "updating\n";
      $query = "UPDATE $table_name set server='$server', port='$port',
          email='$email', passwd='$passwd', 
          protocol='$protocol', offset='$offset', category='$category' WHERE
          email='$email' AND server='$server'";
    }
    $results = $wpdb->query($wpdb->prepare($query));
  }
}

function fetch_mails() {
  global $wpdb;
  /* checks mail from various mailboxes and posts those e-mails */
  //Load up some usefull libraries
    
  //Retreive emails 
  $fetch_query = 'SELECT * FROM ' .  $wpdb->prefix . 'postie_addresses';
  $mailboxes=$wpdb->get_results($fetch_query);
  print_r($mailboxes);
  $config=get_config();
  foreach ($mailboxes as $mailbox) {
    $emails = FetchMail($mailbox->server, $mailbox->port,
        $mailbox->email, $mailbox->passwd, $mailbox->protocol);
    //loop through messages
    foreach ($emails as $email) {
      //sanity check to see if there is any info in the message
      if ($email == NULL ) {
        print 'Dang, message is empty!'; 
        continue; 
      }
      
      $mimeDecodedEmail = DecodeMimeMail($email);
      $from = RemoveExtraCharactersInEmailAddress(
          trim($mimeDecodedEmail->headers["from"]));

      //Check poster to see if a valid person
      $poster = ValidatePoster($mimeDecodedEmail, $config);
      if (!empty($poster)) {
        if ($config['TEST_EMAIL']) 
          DebugEmailOutput($email,$mimeDecodedEmail); 
        if ($mailbox->category)
          $config['DEFAULT_POST_CATEGORY'] = $mailbox->category;
        PostEmail($poster,$mimeDecodedEmail, $config);
      }
      else {
        print("<p>Ignoring email - not authorized.\n");
      }
    } // end looping over messages
  }
}
?>
