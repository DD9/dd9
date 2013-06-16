<div class="wrap"> 
    <div style="float:right; width: 220px; border: 1px solid darkgrey; padding:2px;border-radius:10px;margin-left: 10px;" >
        <p class="" style="text-align:center;font-weight: bolder; margin-top: 0px; margin-bottom: 2px;">Please Donate, Every $ Helps!</p>
        <p style="margin-top: 0;margin-bottom: 2px;">Your generous donation allows me to continue developing Postie for the WordPress community.</p>
        <form style="" action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="HPK99BJ88V4C2">
            <div style="text-align:center;">
                <input style="border: none; margin: 0;" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
            </div>
        </form>
    </div>
    <h2>
        <a style='text-decoration:none' href='options-general.php?page=postie/postie.php'>
            <?php
            echo '<img src="' . plugins_url('images/mail.png', __FILE__) . '" alt="postie" />';
            _e('Postie Settings', 'postie');
            ?>
        </a>
        <span class="description">(v<?php _e(POSTIE_VERSION, 'postie'); ?>)</span>
    </h2>


    <?php
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'postie-functions.php');
    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "reset":
                config_ResetToDefault();
                $message = 1;
                break;
            case "cronless":
                check_postie();
                $message = 1;
                break;
            case "test":
                include(POSTIE_ROOT . '/postie_test.php');
                exit;
                break;
            case "runpostie":
                EchoInfo("Checking for mail manually");
                include(POSTIE_ROOT . '/get_mail.php');
                exit;
                break;
            case "runpostie-debug":
                EchoInfo("Checking for mail manually with debug output");
                if (!defined('POSTIE_DEBUG')) {
                    define('POSTIE_DEBUG', true);
                }
                include(POSTIE_ROOT . '/get_mail.php');
                exit;
                break;
            default:
                $message = 2;
                break;
        }
    }
    global $wpdb, $wp_roles;

    $title = __('Postie Options', 'postie');
    $parent_file = 'options-general.php';
    $config = config_Read();
    if (empty($config))
        $config = config_ResetToDefault();

    $arrays = config_ArrayedSettings();
    // some fields are stored as arrays, because that makes back-end processing much easier
    // and we need to convert those fields to strings here, for the options form
    foreach ($arrays as $sep => $fields) {
        foreach ($fields as $field) {
            $config[$field] = implode($sep, $config[$field]);
        }
    }
    extract($config);
    if (!isset($maxemails)) {
        DebugEcho("New setting: maxemails");
        $maxemails = 0;
    }
    if (!isset($category_match)) {
        $category_match = true;
    }

    if ($interval == 'manual') {
        wp_clear_scheduled_hook('check_postie_hook');
    }

    $messages[1] = __("Configuration successfully updated!", 'postie');
    $messages[2] = __("Error - unable to save configuration", 'postie');
    ?>
    <?php if (isset($_GET['message'])) : ?>
        <div class="updated"><p><?php _e($messages[$_GET['message']], 'postie'); ?></p></div>
    <?php endif; ?>

    <form name="postie-options" method='post'> 
        <input type="hidden" name="action" value="runpostie" />
        <input name="Submit" value="<?php _e("Run Postie", 'postie'); ?> &raquo;" type="submit" class='button'>
        <?php _e("(To run the check mail script manually)", 'postie'); ?>
    </form>
    <form name="postie-options" method='post'> 
        <input type="hidden" name="action" value="runpostie-debug" />
        <input name="Submit" value="<?php _e("Run Postie (Debug)", 'postie'); ?> &raquo;" type="submit" class='button'>
        <?php _e("(To run the check mail script manually with full debug output)", 'postie'); ?>
    </form>
    <form name="postie-options" method="post">
        <input type="hidden" name="action" value="test" />
        <input name="Submit" value="<?php _e("Test Config", 'postie'); ?>&raquo;" type="submit" class='button'>
        <?php _e("this will check your configuration options", 'postie'); ?>
    </form>

    <form name="postie-options" method="post" action='options.php' autocomplete="off">
        <?php
        settings_fields('postie-settings');
        ?>
        <input type="hidden" name="action" value="config" />
        <div id="simpleTabs">
            <div class="simpleTabs-nav">
                <ul>
                    <li id="simpleTabs-nav-1"><?php _e('Mailserver', 'postie') ?></li>
                    <li id="simpleTabs-nav-2"><?php _e('User', 'postie') ?></li>
                    <li id="simpleTabs-nav-3"><?php _e('Message', 'postie') ?></li>
                    <li id="simpleTabs-nav-4"><?php _e('Image', 'postie') ?></li>
                    <li id="simpleTabs-nav-5"><?php _e('Video and Audio', 'postie') ?></li>
                    <li id="simpleTabs-nav-6"><?php _e('Attachments', 'postie') ?></li>
                    <li id="simpleTabs-nav-7"><?php _e('Help', 'postie') ?></li>
                    <li id="simpleTabs-nav-8"><?php _e('FAQ', 'postie') ?></li>
                </ul>
            </div>
            <div id="simpleTabs-content-1" class="simpleTabs-content">
                <table class='form-table'>

                    <tr>
                        <th scope="row"><?php _e('Mail Protocol:', 'postie') ?>        </th>
                        <td>
                            <select name='postie-settings[input_protocol]' id='postie-settings-input_protocol'>
                                <option value="pop3"  <?php echo (($input_protocol == "pop3") ? " selected='selected' " : "") ?>>POP3</option>
                                <?php if (HasIMAPSupport(false)): ?>
                                    <option value="imap" <?php echo ($input_protocol == "imap") ? "selected='selected' " : "" ?>>IMAP</option>
                                    <option value="pop3-ssl" <?php echo ($input_protocol == "pop3-ssl") ? "selected='selected' " : "" ?>>POP3-SSL</option>
                                    <option value="imap-ssl" <?php echo ($input_protocol == "imap-ssl") ? "selected='selected' " : "" ?>>IMAP-SSL</option>
                                <?php endif; ?>
                            </select>
                            <?php if (!HasIMAPSupport(false)): ?>
                                <span class="recommendation">IMAP/IMAP-SSL/POP3-SSL unavailable</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php echo BuildBooleanSelect(__("Use Transport Layer Security (TLS)"), 'postie-settings[email_tls]', $email_tls, __("Choose Yes if your server requres TLS")); ?>

                    <tr>
                        <th scope="row"><?php _e('Port:', 'postie') ?><br />
                            <span class='recommendation'><?php _e("Standard Ports:", 'postie'); ?><br />
                                <?php _e("POP3", 'postie'); ?> - 110<br />
                                <?php _e("IMAP", 'postie'); ?> - 143<br />
                                <?php _e("IMAP-SSL", 'postie'); ?>- 993 <br />
                                <?php _e("POP3-SSL", 'postie'); ?> - 995 <br />
                            </span>
                        </th>
                        <td valign="top">
                            <br/><input name='postie-settings[mail_server_port]' type="text" id='postie-settings-mail_server_port' value="<?php echo esc_attr($mail_server_port); ?>" size="6" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Mail Server:', 'postie') ?></th>
                        <td><input name='postie-settings[mail_server]' type="text" id='postie-settings-mail_server' value="<?php echo esc_attr($mail_server); ?>" size="40" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th width="33%" scope="row"><?php _e('Mail Userid:', 'postie') ?></th>
                        <td><input name='postie-settings[mail_userid]' type="text" id='postie-settings-mail_userid' autocomplete='off' value="<?php echo esc_attr($mail_userid); ?>" size="40" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Mail Password:', 'postie') ?></th>
                        <td>
                            <input name='postie-settings[mail_password]' type="password" id='postie-settings-mail_password' autocomplete='off' value="<?php echo esc_attr($mail_password); ?>" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Postie Time Correction:', 'postie') ?>
                            <br />
                            <span class='recommendation'><?php _e("Should be the same as your normal offset - but this lets you adjust it in cases where that doesn't work.", 'postie'); ?></span>
                        </th>
                        <td><input name='postie-settings[time_offset]' type="text" id='postie-settings-time_offset' size="2" value="<?php echo esc_attr($time_offset); ?>" /> 
                            <?php _e('hours', 'postie') ?> 

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Check for mail every', 'postie') ?>
                        </th>
                        <td>
                            <select name='postie-settings[interval]' id='postie-settings-interval'>
                                <option value="weekly" <?php
                                if ($interval == "weekly") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('Once weekly', 'postie') ?></option>
                                <option value="daily"<?php
                                if ($interval == "daily") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('daily', 'postie') ?></option>
                                <option value="hourly" <?php
                                if ($interval == "hourly") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('hourly', 'postie') ?></option>
                                <option value="twiceperhour" <?php
                                if ($interval == "twiceperhour") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('twice per hour', 'postie') ?></option>
                                <option value="tenminutes" <?php
                                if ($interval == "tenminutes") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('every ten minutes', 'postie') ?></option>
                                <option value="fiveminutes" <?php
                                if ($interval == "fiveminutes") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('every five minutes', 'postie') ?></option>
                                <option value="manual" <?php
                                if ($interval == "manual") {
                                    echo "selected='selected'";
                                }
                                ?>><?php _e('check manually', 'postie') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Maximum number of emails to process:', 'postie'); ?>
                        </th>
                        <td>
                            <select name='postie-settings[maxemails]' id='postie-settings-maxemails'>
                                <option value="0" <?php if ($maxemails == '0') echo "selected='selected'" ?>>All</option>
                                <option value="1" <?php if ($maxemails == '1') echo "selected='selected'" ?>>1</option>
                                <option value="2" <?php if ($maxemails == '2') echo "selected='selected'" ?>>2</option>
                                <option value="5" <?php if ($maxemails == '5') echo "selected='selected'" ?>>5</option>
                                <option value="10" <?php if ($maxemails == '10') echo "selected='selected'" ?>>10</option>
                                <option value="25" <?php if ($maxemails == '25') echo "selected='selected'" ?>>25</option>
                                <option value="50" <?php if ($maxemails == '50') echo "selected='selected'" ?>>50</option>
                            </select>
                        </td>
                    </tr>
                    <?php echo BuildBooleanSelect(__("Delete email after posting"), 'postie-settings[delete_mail_after_processing]', $delete_mail_after_processing, __("Only set to no for testing purposes")); ?>
                </table>
            </div>

            <div id="simpleTabs-content-2" class="simpleTabs-content">
                <table class='form-table'>

                    <?php echo BuildBooleanSelect(__("Allow Anyone To Post Via Email"), "postie-settings[turn_authorization_off]", $turn_authorization_off, "Changing this to yes <b style='color: red'>is not recommended</b> - anything that gets sent in will automatically be posted."); ?>
                    <tr>
                        <th scope="row">
                            <?php _e('Roles That Can Post:', 'postie') ?><br />
                            <span class='recommendation'><?php _e("This allows you to grant access to other users to post if they have the proper access level. Administrators can always post.", 'postie'); ?></span>
                        </th>
                        <td>
                            <br />
                            <table class="checkbox-table">
                                <?php
                                foreach ($wp_roles->role_names as $roleId => $name) {
                                    $name = translate_user_role($name);
                                    $role = $wp_roles->get_role($roleId);
                                    if ($roleId != "administrator") {
                                        ?>
                                        <tr>
                                            <td>
                                                <input type='checkbox' value='1' name='postie-settings[role_access][<?php echo $roleId; ?>]' <?php echo ($role->has_cap("post_via_postie")) ? 'checked="checked"' : "" ?>  >
                                                <?php echo $name; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } else {
                                        ?>
                                        <tr>
                                            <td>
                                                <input type='checkbox' value='1' disabled='disabled' checked='checked' > <?php echo $name; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        </td>
                    </tr>

                    <?php echo BuildTextArea("Authorized Addresses", "postie-settings[authorized_addresses]", $authorized_addresses, "Put each email address on a single line. Posts from emails in this list will be treated as if they came from the admin. If you would prefer to have users post under their own name - create a WordPress user with the correct access level."); ?>
                    <tr> 
                        <th width="33%" valign="top" scope="row">
                            <?php _e('Default Poster:') ?> <br />
                            <span class='recommendation'><?php _e("This will be the poster if you allow posting from emails that are not a registered blog user.", 'postie'); ?></span>
                        </th> 
                        <td>
                            <select name='postie-settings[admin_username]' id='postie-settings[admin_username]'>
                                <?php
                                $adminusers = get_users('orderby=nicename&role=administrator');
                                foreach ($adminusers as $user) {
                                    $selected = "";
                                    if ($user->user_login == $admin_username) {
                                        $selected = " selected='selected'";
                                    }
                                    echo "<option value='$user->user_login'$selected>$user->user_nicename ($user->user_login)</option>";
                                }
                                ?>
                            </select>
                        </td> 
                    </tr> 
                </table> 
            </div>

            <div id = "simpleTabs-content-3" class = "simpleTabs-content">
                <table class = 'form-table'>
                    <tr valign = "top">
                        <th scope = "row"><?php _e('Default post by mail category:', 'postie') ?></th>
                        <td>
                            <?php
                            $defaultCat = $default_post_category;
                            $args = array('name' => 'postie-settings[default_post_category]', 'hierarchical' => 1, 'selected' => $defaultCat, 'hide_empty' => 0);
                            wp_dropdown_categories($args);
                            ?>
                    </tr>
                    <?php echo BuildBooleanSelect("Match short category", "postie-settings[category_match]", $category_match, "Try to match categories using 'starts with logic' otherwise only do exact matches"); ?>

                    <tr valign="top">
                        <th scope="row">
                            <?php _e('Default post by mail tag(s)', 'postie') ?><br />
                            <span
                                class='recommendation'><?php _e('separated by commas', 'postie') ?>
                            </span>
                        </th>
                        <td>
                            <input type='text' name='postie-settings[default_post_tags]' id='postie-settings-default_post_tags' value='<?php echo esc_attr($default_post_tags) ?>' />
                        </td>
                    </tr>

                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Default Post Status:', 'postie') ?> </th> 
                        <td>
                            <select name='postie-settings[post_status]' id='postie-settings-post_status'>
                                <option value="publish" <?php
                                if ($post_status == "publish") {
                                    echo "selected";
                                }
                                ?>>Published</option>
                                <option value="draft" <?php
                                if ($post_status == "draft") {
                                    echo "selected";
                                }
                                ?>>Draft</option>
                                <option value="pending" <?php
                                if ($post_status == "pending") {
                                    echo "selected";
                                }
                                ?>>Pending Review</option>
                                <option value="private" <?php
                                if ($post_status == "private") {
                                    echo "selected";
                                }
                                ?>>Private</option>
                            </select>               
                        </td> 
                    </tr> 

                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Default Post Format:', 'postie') ?> </th> 
                        <td>
                            <select name='postie-settings[post_format]' id='postie-settings-post_format'>
                                <?php
                                $formats = get_theme_support('post-formats');
                                if (is_array($formats[0])) {
                                    $formats = $formats[0];
                                } else {
                                    $formats = array();
                                }
                                array_unshift($formats, "standard");
                                foreach ($formats as $format) {
                                    $selected = "";
                                    if ($config['post_format'] == $format) {
                                        $selected = " selected='selected'";
                                    }
                                    echo "<option value='$format'$selected>$format</option>";
                                }
                                ?>
                            </select>               
                        </td> 
                    </tr> 

                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Default Post Type:', 'postie') ?> </th> 
                        <td>
                            <select name='postie-settings[post_type]' id='postie-settings-post_type'>
                                <?php
                                $types = get_post_types();
                                //array_unshift($types, "standard");
                                foreach ($types as $type) {
                                    $selected = "";
                                    if ($config['post_type'] == $type) {
                                        $selected = " selected='selected'";
                                    }
                                    echo "<option value='$type'$selected>$type</option>";
                                }
                                ?>
                            </select>               
                        </td> 
                    </tr> 

                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Default Title:', 'postie') ?> </th> 
                        <td>
                            <input name='postie-settings[default_title]' type="text" id='postie-settings-default_title' value="<?php echo esc_attr($default_title); ?>" size="50" /><br />
                        </td> 
                    </tr> 
                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Preferred Text Type:', 'postie') ?> </th> 
                        <td>
                            <select name='postie-settings[prefer_text_type]' id='postie-settings-prefer_text_type'>
                                <?php printf('<option value="plain" %s>plain</option>', ($prefer_text_type == "plain") ? "selected" : "") ?>
                                <?php printf('<option value="html" %s>html</option>', ($prefer_text_type == "html") ? "selected" : "") ?>
                            </select>
                        </td> 
                    </tr> 
                    <?php echo BuildBooleanSelect("Forward Rejected Mail", "postie-settings[forward_rejected_mail]", $forward_rejected_mail); ?>
                    <?php echo BuildBooleanSelect("Allow Subject In Mail", "postie-settings[allow_subject_in_mail]", $allow_subject_in_mail); ?>
                    <?php echo BuildBooleanSelect("Allow HTML In Mail Subject", "postie-settings[allow_html_in_subject]", $allow_html_in_subject); ?>
                    <?php echo BuildBooleanSelect("Allow HTML In Mail Body", "postie-settings[allow_html_in_body]", $allow_html_in_body); ?>
                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Tag Of Message Start:', 'postie') ?> <br />
                            <span class='recommendation'><?php _e('Use to remove any text from a message that the email provider puts at the top of the message', 'postie') ?></span></th>
                        <td>
                            <input name='postie-settings[message_start]' type="text" id='postie-settings-message_start' value="<?php echo esc_attr($message_start); ?>" size="50" /><br />
                        </td> 
                    </tr>
                    <tr>
                        <th width="33%" valign="top" scope="row"><?php _e('Tag Of Message End:', 'postie') ?> <br />
                            <span class='recommendation'><?php _e('Use to remove any text from a message that the email provider puts at the end of the message', 'postie') ?></span></th>
                        <td>
                            <input name='postie-settings[message_end]' type="text" id='postie-settings-message_end' value="<?php echo esc_attr($message_end); ?>" size="50" /><br />
                        </td>
                    </tr>

                    <?php
                    echo BuildBooleanSelect("Wrap content in pre tags", "postie-settings[wrap_pre]", $wrap_pre);
                    echo BuildBooleanSelect("Filter newlines", "postie-settings[filternewlines]", $filternewlines, "Retain newlines from plain text. Set to no if using markdown or textitle syntax");
                    echo BuildBooleanSelect("Replace newline characters with html line breaks (&lt;br /&gt;)", "postie-settings[convertnewline]", $convertnewline, "Filter newlines must be turned on for this option to take effect.");
                    echo BuildBooleanSelect("Return rejected mail to sender", "postie-settings[return_to_sender]", $return_to_sender);
                    ?>
                    <tr>
                        <th>
                            <?php _e("Send post confirmation e-mail to", 'postie') ?>
                        </th>
                        <td>
                            <select name='postie-settings[confirmation_email]' id='postie-settings-confirmation_email'>
                                <option value="sender" <?php echo($confirmation_email == "sender") ? "selected" : "" ?>><?php _e('sender', 'postie') ?></option>
                                <option value="admin" <?php echo ($confirmation_email == "admin") ? "selected" : "" ?>><?php _e('administrator', 'postie') ?></option>
                                <option value="both" <?php echo ($confirmation_email == "both") ? "selected" : "" ?>><?php _e('sender and administrator', 'postie') ?></option>
                                <option value="" <?php echo ($confirmation_email == "") ? "selected" : "" ?>><?php _e('none', 'postie') ?></option>
                            </select>
                        </td>
                    </tr>

                    <?php
                    echo BuildBooleanSelect("Automatically convert urls to links", "postie-settings[converturls]", $converturls);
                    echo BuildBooleanSelect("Use shortcode for embedding video (youtube and others)", "postie-settings[shortcode]", $shortcode);
                    ?>
                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Encoding for pages and feeds:', 'postie') ?> </th> 
                        <td>
                            <input name='postie-settings[message_encoding]' type="text" id='postie-settings-message_encoding' value="<?php echo esc_attr($message_encoding); ?>" size="10" />
                            <span class='recommendation'>UTF-8 <?php _e("should handle ISO-8859-1 as well", 'postie'); ?></span>
                        </td> 
                    </tr> 
                    <?php echo BuildBooleanSelect("Decode Quoted Printable Data", "postie-settings[message_dequote]", $message_dequote); ?>
                    <?php echo BuildTextArea("Supported MIME Types", "postie-settings[supported_file_types]", $supported_file_types, "Add just the type (not the subtype). Text, Video, Audio, Image and Multipart are always supported. Put each type on a single line."); ?>
                    <?php echo BuildTextArea("Banned File Names", "postie-settings[banned_files_list]", $banned_files_list, "Put each file name on a single line.Files matching this list will never be posted to your blog. You can use wildcards such as *.xls, or *.* for all files"); ?>
                    <?php echo BuildBooleanSelect("Drop The Signature From Mail", "postie-settings[drop_signature]", $drop_signature); ?>
                    <?php echo BuildTextArea("Signature Patterns", "postie-settings[sig_pattern_list]", $sig_pattern_list, "Put each pattern on a separate line. Patterns are <a href='http://regex101.com/' target='_blank'>regular expressions</a>."); ?>
                    <?php echo BuildTextArea("Allowed SMTP servers", "postie-settings[smtp]", $smtp, "Only allow messages which have been sent throught the following smtp servers. Put each server on a separate line. Leave blank to not check smtp servers."); ?>
                </table> 
            </div>
            <div id="simpleTabs-content-4" class="simpleTabs-content">
                <table class='form-table'>

                    <?php
                    echo BuildBooleanSelect("Use First Image as Featured Image", "postie-settings[featured_image]", $featured_image, "If any images are attached, the first one will be the featured image for the post");
                    echo BuildBooleanSelect("Automatically insert image gallery", "postie-settings[auto_gallery]", $auto_gallery, "If any images are attached, they will automatically be inserted as a gallery");
                    echo BuildBooleanSelect("Post Images At End", "postie-settings[images_append]", $images_append, "No means they will be put before the text of the message.");
                    echo BuildBooleanSelect("Start Image Count At 0", "postie-settings[start_image_count_at_zero]", $start_image_count_at_zero);
                    echo BuildBooleanSelect("Generate Thumbnails", "postie-settings[generate_thumbnails]", $generate_thumbnails, "Some hosts crash during thumbnail generation. Set this to 'No' if you have this issue.");
                    ?>
                    <tr> 
                        <th width="33%" valign="top" scope="row"><?php _e('Image Place Holder Tag:', 'postie') ?> </th> 
                        <td>
                            <input name='postie-settings[image_placeholder]' type="text" id='postie-settings-image_placeholder' value="<?php echo esc_attr($image_placeholder); ?>" size="50" /><br />
                        </td> 
                    </tr> 
                    <tr>
                        <th width="33%" valign="top" scope="row"><?php _e('Image Template', 'postie') ?><br />
                            <span class='recommendation'><?php _e('Choose a default template, then customize to your liking in the text box', 'postie'); ?></span><br /><br />
                            <span class='recommendation'><?php _e('Note that this template are only used if the "Preferred Text Type" setting is set to "plain"', 'postie'); ?></span><br /><br />
                            <span class='recommendation'><?php _e('Sizes for thumbnail, medium, and large images can be chosen in the <a href="options-media.php">Media Settings</a>. The samples here use the default sizes, and will not reflect the sizes you have chosen.', 'postie'); ?></span>
                        </th>
                        <td>
                            <input type='hidden' id='postie-settings-selected_imagetemplate' name='postie-settings[selected_imagetemplate]'
                                   value="<?php echo esc_attr($selected_imagetemplate) ?>" />
                            <select name='imagetemplateselect' id='imagetemplateselect' 
                                    onchange="changeStyle('imageTemplatePreview', 'postie-settings-imagetemplate',
                                                    'imagetemplateselect', 'postie-settings-selected_imagetemplate', 'smiling.jpg');" >
                                        <?php
                                        include(POSTIE_ROOT . '/templates/image_templates.php');
                                        $styleOptions = $imageTemplates;
                                        $selected = $selected_imagetemplate;
                                        foreach ($styleOptions as $key => $value) {
                                            if ($key != 'selected') {
                                                if ($key == $selected) {
                                                    $select = ' selected=selected ';
                                                } else {
                                                    $select = ' ';
                                                }
                                                if ($key == 'custom')
                                                    $value = $imagetemplate;
                                                echo '<option' . $select . 'value="' .
                                                esc_attr($value) . '" >' . $key . '</option>';
                                            }
                                        }
                                        ?>
                            </select>
                            <div>
                                <?php _e('Preview', 'postie'); ?>
                            </div>
                            <div id='imageTemplatePreview'></div>
                            <textarea onchange='changeStyle("imageTemplatePreview", "postie-settings-imagetemplate", "imagetemplateselect",
                                                    "postie-settings-selected_imagetemplate", "smiling.jpg", true);' cols='70' rows='7' id='postie-settings-imagetemplate' name='postie-settings[imagetemplate]'>
                                      <?php echo esc_attr($imagetemplate) ?>
                            </textarea>
                            <div class='recommendation'>
                                <ul>
                                    <li>{CAPTION} gets replaced with the caption you specified (if any)</li>
                                    <li>{FILELINK} gets replaced with the url to the media</li>
                                    <li>{FILENAME} gets replaced with the name of the attachment from the email</li>
                                    <li>{FULL} same as {FILELINK}</li>
                                    <li>{HEIGHT} gets replaced with the height of the photo</li>
                                    <li>{ID} gets replaced with the post id</li>
                                    <li>{IMAGE} same as {FILELINK}</li>
                                    <li>{LARGEHEIGHT} gets replaced with the height of a large image</li>
                                    <li>{LARGEWIDTH} gets replaced with the width of a large image</li>
                                    <li>{LARGE} gets replaced with the url to the large-sized image</li>
                                    <li>{MEDIUMHEIGHT} gets replaced with the height of a medium image</li>
                                    <li>{MEDIUMWIDTH} gets replaced with the width of a medium image</li>
                                    <li>{MEDIUM} gets replaced with the url to the medium-sized image</li>
                                    <li>{PAGELINK} gets replaced with the URL of the file in WordPress</li>
                                    <li>{POSTTITLE} gets replaced with the post title (subject)</li>
                                    <li>{RELFILENAME} gets replaced with the relative path to the full-size image</li>
                                    <li>{THUMBHEIGHT} gets replaced with the height of a thumbnail image</li>
                                    <li>{THUMB} gets replaced with the url to the thumbnail image</li>
                                    <li>{THUMBNAIL} same as {THUMB}</li>
                                    <li>{THUMBWIDTH} gets replaced with the width of a thumbnail image</li>
                                    <li>{TITLE} same as {POSTTITLE}</li>
                                    <li>{URL} same as {FILELINK}</li>
                                    <li>{WIDTH} gets replaced with width of the photo</li>
                                    <li>{ICON} insert the icon for the attachment (for non-audio/image/video attachments only)</li>
                                </ul>
                            </div>
                        </td>
                    </tr> 
                    <?php echo BuildBooleanSelect("Use custom image field", "postie-settings[custom_image_field]", $custom_image_field, "When true, images will not appear in the post. Instead the url to the image will be input into a custom field named 'image'."); ?>            
                </table> 
            </div> 

            <!-- 
            ##########   VIDEO AND AUDIO OPTIONS ###################
            -->

            <div id="simpleTabs-content-5" class="simpleTabs-content">
                <table class='form-table'>

                    <tr>
                        <th scope='row'><?php _e('Video template 1', 'postie') ?><br />
                            <span class='recommendation'><?php _e('Choose a default template, then customize to your liking in the text box', 'postie') ?></span><br />
                            <span class='recommendation'><?php _e('Note that this template are only used if the "Preferred Text Type" setting is set to "plain"', 'postie'); ?></span><br />
                        </th>
                        <?php $templateDir = get_option('siteurl') . '/' . PLUGINDIR . '/postie/templates'; ?>
                        <td>
                            <input type='hidden' id='postie-settings-selected_video1template' name='postie-settings[selected_video1template]'
                                   value="<?php echo esc_attr($selected_video1template) ?>" />
                            <select name='video1templateselect' id='video1templateselect' 
                                    onchange="changeStyle('video1TemplatePreview', 'postie-settings-video1template', 'video1templateselect', 'postie-settings-selected_video1template', 'hi.mp4');" />
                                    <?php
                                    include(POSTIE_ROOT . '/templates/video1_templates.php');
                                    $styleOptions = $video1Templates;
                                    $selected = $selected_video1template;
                                    foreach ($styleOptions as $key => $value) {
                                        if ($key != 'selected') {
                                            if ($key == $selected) {
                                                $select = ' selected=selected ';
                                            } else {
                                                $select = ' ';
                                            }
                                            if ($key == 'custom')
                                                $value = $video1template;
                                            echo '<option' . $select . 'value="' .
                                            esc_attr($value) . '" >' . $key . '</option>';
                                        }
                                    }
                                    ?>
                            </select>
                            <div>
                                <?php _e('Preview', 'postie'); ?>
                            </div>
                            <div id='video1TemplatePreview'></div>
                            <textarea onchange="changeStyle('video1TemplatePreview', 'postie-settings-video1template',
                                                    'video1templateselect', 'postie-settings-selected_video1template', 'hi.mp4', true);" cols='70' rows='7' id='postie-settings-video1template'
                                      name='postie-settings[video1template]'><?php echo esc_attr($video1template) ?></textarea>
                        </td>
                    </tr>
                    <tr> 
                        <th width="33%" valign="top" scope="row">
                            <?php _e('Video 1 file extensions:') ?><br /><span class='recommendation'>
                                <?php _e('Use video template 1 for files with these extensions (separated by commas)', 'postie') ?></span> </th> 
                        <td>
                            <br/><input name='postie-settings[video1types]' type="text" id='postie-settings-video1types'
                                        value="<?php if ($video1types != '') echo esc_attr($video1types); ?>" size="40" />                
                        </td> 
                    </tr> 
                    <tr><td colspan="2"><hr /></td></tr>
                    <tr>
                        <th scope='row'><?php _e('Video template 2', 'postie') ?><br />
                            <span class='recommendation'><?php _e('Choose a default template, then customize to your liking in the text box', 'postie') ?></span><br/>
                            <span class='recommendation'><?php _e('Note that this template are only used if the "Preferred Text Type" setting is set to "plain"', 'postie'); ?></span><br />
                        </th>
                        <td>
                            <input type='hidden' id='postie-settings-selected_video2template' name='postie-settings[selected_video2template]'
                                   value="<?php echo esc_attr($selected_video2template) ?>" />
                            <select name='video2templateselect' id='video2templateselect' 
                                    onchange="changeStyle('video2TemplatePreview', 'postie-settings-video2template',
                                                    'video2templateselect', 'postie-settings-selected_video2template', 'hi.flv');" >
                                        <?php
                                        include(POSTIE_ROOT . '/templates/video2_templates.php');
                                        $styleOptions = $video2Templates;
                                        $selected = $selected_video2template;
                                        foreach ($styleOptions as $key => $value) {
                                            if ($key != 'selected') {
                                                if ($key == $selected) {
                                                    $select = ' selected=selected ';
                                                } else {
                                                    $select = ' ';
                                                }
                                                if ($key == 'custom')
                                                    $value = $video2template;
                                                echo '<option' . $select . 'value="' . esc_attr($value) . '" >' . $key . '</option>';
                                            }
                                        }
                                        ?>
                            </select>
                            <div>
                                <?php _e('Preview', 'postie'); ?>
                            </div>
                            <div id='video2TemplatePreview'></div>
                            <textarea onchange="changeStyle('video2TemplatePreview', 'postie-settings-video2template',
                                                    'video2templateselect', 'postie-settings-selected_video2template', 'hi.flv', true);" cols='70' rows='7' id='postie-settings-video2template'
                                      name='postie-settings[video2template]'>
                                          <?php echo esc_attr($video2template) ?>
                            </textarea>
                        </td>
                    </tr>
                    <tr> 
                        <th width="33%" valign="top" scope="row">
                            <?php _e('Video 2 file extensions:') ?><br /><span class='recommendation'>
                                <?php _e('Use video template 2 for files with these extensions (separated by commas)', 'postie') ?></span> <br />
                        </th> 
                        <td>
                            <br/><input name='postie-settings[video2types]' type="text" id='postie-settings-video2types'
                                        value="<?php if ($video2types != '') echo esc_attr($video2types); ?>" size="40" />                
                        </td> 
                    </tr> 
                    <tr><td colspan="2"><hr /></td></tr>
                    <tr>
                        <th scope='row'><?php _e('Audio template', 'postie') ?><br />
                            <span class='recommendation'><?php _e('Choose a default template, then customize to your liking in the text box', 'postie') ?></span>
                            <br />
                            <span class='recommendation'><?php _e('Note that this template are only used if the "Preferred Text Type" setting is set to "plain"', 'postie'); ?></span><br />
                        </th>
                        <td>
                            <input type='hidden' id='postie-settings-selected_audiotemplate' name='postie-settings[selected_audiotemplate]'
                                   value="<?php echo esc_attr($selected_audiotemplate) ?>" />
                            <select name='audiotemplateselect' id='audiotemplateselect' 
                                    onchange="changeStyle('audioTemplatePreview', 'postie-settings-audiotemplate',
                                                    'audiotemplateselect', 'postie-settings-selected_audiotemplate', 'funky.mp3', false);" >
                                        <?php
                                        include(POSTIE_ROOT . '/templates/audio_templates.php');
                                        $styleOptions = $audioTemplates;
                                        $selected = $selected_audiotemplate;
                                        foreach ($styleOptions as $key => $value) {
                                            if ($key != 'selected') {
                                                if ($key == $selected) {
                                                    $select = ' selected=selected ';
                                                } else {
                                                    $select = ' ';
                                                }
                                                if ($key == 'custom')
                                                    $value = $audiotemplate;
                                                echo '<option' . $select . 'value="' .
                                                esc_attr($value) . '" >' . $key . '</option>';
                                            }
                                        }
                                        ?>
                            </select>
                            <div>
                                <?php _e('Preview', 'postie'); ?>
                            </div>
                            <div id='audioTemplatePreview'></div>
                            <textarea onchange="changeStyle('audioTemplatePreview', 'postie-settings-audiotemplate',
                                                    'audiotemplateselect', 'postie-settings-selected_audiotemplate', 'funky.mp3', true);" cols='70' rows='7' id='postie-settings-audiotemplate'
                                      name='postie-settings[audiotemplate]'><?php echo esc_attr($audiotemplate) ?></textarea>
                        </td>
                    </tr>
                    <tr> 
                        <th width="33%" valign="top" scope="row">
                            <?php _e('Audio file extensions:') ?><br />
                            <span class='recommendation'><?php _e('Use the audio template for files with these extensions (separated by commas)', 'postie') ?></span> <br />

                        </th> 
                        <td>
                            <br/><input name='postie-settings[audiotypes]' type="text" id='postie-settings-audiotypes' value="<?php echo esc_attr($audiotypes); ?>" size="40" />
                        </td> 
                    </tr> 
                </table> 
            </div>
            <div id="simpleTabs-content-6" class="simpleTabs-content">
                <table class='form-table'>

                    <tr>
                        <th scope='row'><?php _e('Attachment icon set', 'postie') ?><br /></th>
                        <td>
                            <input type='hidden' id='postie-settings-icon_set' name='postie-settings[icon_set]'
                                   value="<?php echo esc_attr($icon_set) ?>" />

                            <?php
                            $icon_sets = array('silver', 'black', 'white', 'custom', 'none');
                            $icon_sizes = array(32, 48, 64);
                            ?>
                            <select name='icon_set_select' id='icon_set_select'  onchange="changeIconSet(this);" >
                                <?php
                                $styleOptions = $icon_sets;
                                $selected = $icon_set;
                                foreach ($styleOptions as $key) {
                                    if ($key != 'selected') {
                                        if ($key == $selected) {
                                            $select = ' selected=selected ';
                                        } else {
                                            $select = ' ';
                                        }
                                        echo '<option' . $select . 'value="' . esc_attr($key) . '" >' . $key . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div id='postie-settings-attachment_preview'></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope='row'><?php _e('Attachment icon size (in pixels)', 'postie') ?><br /></th>
                        <td>
                            <input type='hidden' id='postie-settings-icon_size' name='postie-settings[icon_size]'
                                   value="<?php echo esc_attr($icon_size) ?>" />
                            <select name='icon_size_select' id='icon_size_select' onchange="changeIconSet(this, true);" >
                                <?php
                                $styleOptions = $icon_sizes;
                                $selected = $icon_size;
                                foreach ($styleOptions as $key) {
                                    if ($key != 'selected') {
                                        if ($key == $selected) {
                                            $select = ' selected=selected ';
                                        } else {
                                            $select = ' ';
                                        }
                                        echo '<option' . $select . 'value="' . esc_attr($key) . '" >' . $key . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope='row'><?php _e('Attachment template', 'postie') ?><br />
                            <span class='recommendation'><?php _e('Choose a default template, then customize to your liking in the text box', 'postie') ?></span>
                            <br />
                            <span class='recommendation'><?php _e('Note that this template are only used if the "Preferred Text Type" setting is set to "plain"', 'postie'); ?></span><br />
                        </th>
                        <td>
                            <input type='hidden' id='postie-settings-selected_generaltemplate' name='postie-settings[selected_generaltemplate]'
                                   value="<?php echo esc_attr($selected_generaltemplate) ?>" />
                            <select name='generaltemplateselect' id='generaltemplateselect' 
                                    onchange="changeStyle('generalTemplatePreview', 'postie-settings-generaltemplate',
                                                    'generaltemplateselect', 'postie-settings-selected_generaltemplate', 'interesting_document.doc', false);" >
                                        <?php
                                        include(POSTIE_ROOT . '/templates/general_template.php');
                                        $styleOptions = $generalTemplates;
                                        $selected = $selected_generaltemplate;
                                        foreach ($styleOptions as $key => $value) {
                                            if ($key != 'selected') {
                                                if ($key == $selected) {
                                                    $select = ' selected="selected" ';
                                                } else {
                                                    $select = ' ';
                                                }
                                                if ($key == 'custom')
                                                    $value = $generaltemplate;
                                                echo '<option' . $select . 'value="' . esc_attr($value) . '" >' . $key . '</option>';
                                            }
                                        }
                                        ?>
                            </select>
                            <div>
                                <?php _e('Preview', 'postie'); ?>
                            </div>
                            <div id='generalTemplatePreview'></div>
                            <textarea onchange="changeStyle('generalTemplatePreview', 'postie-settings-generaltemplate', 'generaltemplateselect', 'postie-settings-selected_generaltemplate', 'interesting_document.doc', true);" 
                                      cols='70' rows='7' 
                                      id='postie-settings-generaltemplate'
                                      name='postie-settings[generaltemplate]'><?php echo esc_attr($generaltemplate) ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th style='height:64px'>
                            <?php _e('Preview', 'postie'); ?>
                        </th>
                        <td>

                        </td>
                    </tr>
                </table> 
            </div>
            <div id="simpleTabs-content-7" class="simpleTabs-content">
                <?php include(POSTIE_ROOT . '/readme.html'); ?>
            </div>
            <div id="simpleTabs-content-8" class="simpleTabs-content">
                <?php include(POSTIE_ROOT . '/faq.html'); ?>
            </div>

            <p class="submit">
                <input type="hidden" name="action" value="update" />

                <input type="hidden" name="page_options"
                       value="postie-settings" />

                <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" class="button button-primary" />

            </p>
    </form> 
    <form name="postie-options" method="post"> 
        <input type="hidden" name="action" value="reset" />
        <input name="Submit" value="<?php _e("Reset Settings To Defaults", 'postie') ?>" type="submit" class='button'> (Your Mail server settings will be retained)
    </form>
</div>

<?php $iconDir = get_option('siteurl') . '/' . PLUGINDIR . '/postie/icons'; ?>
<script type="text/javascript">
                                        jQuery(document).ready(function() {
                                            jQuery("#simpleTabs").simpleTabs({
                                                fadeSpeed: "medium", // @param : low, medium, fast
                                                defautContent: 1, // @param : number ( simpleTabs-nav-number)
                                                autoNav: "false", // @param : true or false
                                                closeTabs: "false"   // @param : true or false;
                                            });

                                        });

                                        function changeIconSet(selectBox, size) {
                                            var iconSet = document.getElementById('postie-settings-icon_set');
                                            var iconSize = document.getElementById('postie-settings-icon_size');
                                            var preview = document.getElementById('postie-settings-attachment_preview');
                                            var iconDir = '<?php echo $iconDir ?>/';
                                            if (size == true) {
                                                var hiddenInput = iconSize
                                            } else {
                                                var hiddenInput = iconSet;
                                            }
                                            for (i = 0; i < selectBox.options.length; i++) {
                                                if (selectBox.options[i].selected == true) {
                                                    hiddenInput.value = selectBox.options[i].value;
                                                }
                                            }
                                            var fileTypes = new Array('doc', 'pdf', 'xls', 'default');
                                            preview.innerHTML = '';
                                            for (j = 0; j < fileTypes.length; j++) {
                                                preview.innerHTML += "<img src='" + iconDir + iconSet.value + '/' +
                                                        fileTypes[j] + '-' + iconSize.value + ".png' />";
                                            }
                                        }

                                        function changeStyle(preview, template, select, selected, sample, custom) {
                                            var preview = document.getElementById(preview);
                                            var pageStyles = document.getElementById(select);
                                            var selectedStyle;
                                            var hiddenStyle = document.getElementById(selected);
                                            var pageStyle = document.getElementById(template);
                                            if (custom == true) {
                                                selectedStyle = pageStyles.options[pageStyles.options.length - 1];
                                                selectedStyle.value = pageStyle.value;
                                                selectedStyle.selected = true;
                                            } else {
                                                for (i = 0; i < pageStyles.options.length; i++) {
                                                    if (pageStyles.options[i].selected == true) {
                                                        selectedStyle = pageStyles.options[i];
                                                    }
                                                }
                                            }
                                            hiddenStyle.value = selectedStyle.innerHTML
                                            var previewHTML = selectedStyle.value;
                                            var fileLink = '<?php echo $templateDir ?>/' + sample;
                                            var thumb = '<?php echo $templateDir ?>/' + sample.replace(/\.jpg/,
                                                    '-150x150.jpg');
                                            var medium = '<?php echo $templateDir ?>/' + sample.replace(/\.jpg/,
                                                    '-300x200.jpg');
                                            var large = '<?php echo $templateDir ?>/' + sample.replace(/\.jpg/,
                                                    '-1024x682.jpg');
                                            previewHTML = previewHTML.replace(/{FILELINK}/g, fileLink);
                                            previewHTML = previewHTML.replace(/{IMAGE}/g, fileLink);
                                            previewHTML = previewHTML.replace(/{FILENAME}/, sample);
                                            previewHTML = previewHTML.replace(/{THUMB(NAIL|)}/, thumb);
                                            previewHTML = previewHTML.replace(/{MEDIUM}/, medium);
                                            previewHTML = previewHTML.replace(/{LARGE}/, large);
                                            previewHTML = previewHTML.replace(/{CAPTION}/g, 'Spencer smiling');
                                            preview.innerHTML = previewHTML;
                                            pageStyle.value = selectedStyle.value;
                                        }

                                        function showAdvanced(advancedId, arrowId) {
                                            var advanced = document.getElementById(advancedId);
                                            var arrow = document.getElementById(arrowId);
                                            if (advanced.style.display == 'none') {
                                                advanced.style.display = 'block';
                                                arrow.innerHTML = '&#9660;';
                                            } else {
                                                advanced.style.display = 'none';
                                                arrow.innerHTML = '&#9654;';
                                            }
                                        }

                                        changeStyle('imageTemplatePreview', 'postie-settings-imagetemplate', 'imagetemplateselect', 'postie-settings-selected_imagetemplate', 'smiling.jpg', false);
                                        changeStyle('audioTemplatePreview', 'postie-settings-audiotemplate', 'audiotemplateselect', 'postie-settings-selected_audiotemplate', 'funky.mp3', false);
                                        changeStyle('video1TemplatePreview', 'postie-settings-video1template', 'video1templateselect', 'postie-settings-selected_video1template', 'hi.mp4', false);
                                        changeStyle('video2TemplatePreview', 'postie-settings-video2template', 'video2templateselect', 'postie-settings-selected_video2template', 'hi.flv', false);
                                        changeIconSet(document.getElementById('icon_set_select'));
</script>
