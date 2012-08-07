<?php
/*
Plugin Name: WP-Protect
Plugin URI: http://www.littlebizzy.com/plugins/wp-protect/
Description: Protect your WordPress site against right clicks, text selection, and image dragging. (Now works with AJAX and jQuery lighboxes!)
Version: 1.4
Author: Little Bizzy
Author URI: http://www.littlebizzy.com/
*/

function WPP_OPT() {
if(!isset($_POST['wpprotect_update'])){
$_POST['wpprotect_update'] = "";
}
if(!isset($_POST['wpp_rightclick'])){
$_POST['wpp_rightclick'] = "";
}
if(!isset($_POST['wpp_textselect'])){
$_POST['wpp_textselect'] = "";
}
if(!isset($_POST['wpp_dragging'])){
$_POST['wpp_dragging'] = "";
}
if(!isset($_POST['wpp_warning'])){
$_POST['wpp_warning'] = "";
}
if($_POST['wpprotect_update']){
update_option('wpp_rightclick',$_POST['wpp_rightclick']);
update_option('wpp_textselect',$_POST['wpp_textselect']);
update_option('wpp_dragging',$_POST['wpp_dragging']);
update_option('wpp_warning',$_POST['wpp_warning']);
}
$wp_wpp_rightclick = get_option('wpp_rightclick');
$wp_wpp_textselect = get_option('wpp_textselect');
$wp_wpp_dragging = get_option('wpp_dragging');
$wp_wpp_warning = get_option('wpp_warning');
?>
<div class="wrap">
<h2>WP-Protect (Configuration)</h2>
<form method="post" id="WPP_OPT">
<fieldset class="options">
<p><input type="checkbox" id="wpp_rightclick" name="wpp_rightclick" value="wpp_rightclick" <?php if($wp_wpp_rightclick == true) { echo('checked="checked"'); } ?> />&nbsp;&nbsp;Disable right clicking</p>
<p><input type="checkbox" id="wpp_textselect" name="wpp_textselect" value="wpp_textselect" <?php if($wp_wpp_textselect == true) { echo('checked="checked"'); } ?> />&nbsp;&nbsp;Disable text selection</p>
<p><input type="checkbox" id="wpp_dragging" name="wpp_dragging" value="wpp_dragging" <?php if($wp_wpp_dragging == true) { echo('checked="checked"'); } ?> />&nbsp;&nbsp;Disable image dragging</p>
<p><input type="checkbox" id="wpp_warning" name="wpp_warning" value="wpp_warning" <?php if($wp_wpp_warning == true) { echo('checked="checked"'); } ?> />&nbsp;&nbsp;Enable warning message</p>
<p><em>Notice: disabling "image dragging" may conflict with search/input fields, or with certain WordPress plugins/themes.</em></p>
<p><em>If you have a minute, please <a href="http://wordpress.org/extend/plugins/wp-protect/" target="_blank">rate this plugin</a> on WordPress.org... thanks!</em></p>
<p><input type="submit" name="wpprotect_update" class="button-primary" value="Update Settings" /></p>
</fieldset>
</form>
<p>&nbsp;</p>
<p><strong><em>Tired? Try our full-service website management for $33/month with a FREE custom WordPress template. info@littlebizzy.com</em></strong></p>
<p>&nbsp;</p>
<p><ul><li>&bull;&nbsp;<a href="http://www.littlebizzy.com/" target="_blank">Little Bizzy LLC</a> (website management and online marketing)</li>
<li>&bull;&nbsp;<a href="http://www.littlebizzy.com/themes/tube/" target="_blank">FREE Tube Theme</a> (build your own video tube)</li>
<li>&bull;&nbsp;<a href="http://www.littlebizzy.com/plugins/wp-301/" target="_blank">WP-301</a> (easily manage 301 redirects)</li>
<li>&bull;&nbsp;<a href="http://www.urlworth.com/" target="_blank">URLWorth</a> (check the value of your website)</li></ul></p>
</div>
<?php
}

function finish_footer()
{
?>
<script type="text/javascript">
disableSelection(document.body)
</script>
<?php
}

function wpprotect_rightclick()
{
?>
<script language=JavaScript>
var message="";
function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) {
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;}
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}
document.oncontextmenu=new Function("return false")
</script>
<?php
}

function wpprotect_textselect()
{
?>
<script type="text/javascript">
function disableSelection(target){
if (typeof target.onselectstart!="undefined")
target.onselectstart=function(){return false}
else if (typeof target.style.MozUserSelect!="undefined")
target.style.MozUserSelect="none"
else
target.onmousedown=function(){return false}
target.style.cursor = "default"
}
</script>
<?php
}

function wpprotect_dragging()
{
?>
<script type="text/javascript">
var gl;
function doDisableDragging() {
	var evt = gl || window.event,
	imgs,
	i;
	if (evt.preventDefault) {
		imgs = document.getElementsByTagName('img');
		for (i = 0; i < imgs.length; i++) {
			imgs[i].onmousedown = disableDragging;
		}
	}
}

window.onload = function(e) {
	gl = e;
	var check=setInterval("doDisableDragging()",1000);
}

function disableDragging(e) {
	e.preventDefault();
}
</script>
<?php
}

function wpprotect_warning()
{
?>
<div></div>
<?php
}

function wpprotect_footer() {
$wp_wpp_warning = get_option('wpp_warning');
if($wp_wpp_warning == true) { wpprotect_warning(); }
$wp_wpp_textselect = get_option('wpp_textselect');
if($wp_wpp_textselect == true) { finish_footer();
}
}

function wpprotect_header() {
$wp_wpp_textselect = get_option('wpp_textselect');
$wp_wpp_rightclick = get_option('wpp_rightclick');
$wp_wpp_dragging = get_option('wpp_dragging');
$pos = strpos(strtoupper(getenv("REQUEST_URI")), '?preview=true');
if ($pos === false) {
if($wp_wpp_rightclick == true) { wpprotect_rightclick(); }
if($wp_wpp_textselect == true) { wpprotect_textselect(); }
if($wp_wpp_dragging == true) { wpprotect_dragging(); }
}
}

function wpprotect_admin() {
if (function_exists('add_options_page')) {
add_options_page('WP-Protect', 'WP-Protect', 'manage_options', basename(__FILE__), 'WPP_OPT');
}
}

function wpprotect_actions( $links, $file ) {
if( $file == 'wp-protect/wp-protect.php' && function_exists( "admin_url" ) ) {
$settings_link = '<a href="' . admin_url( 'options-general.php?page=wp-protect.php' ) . '">' .'Settings' . '</a>';
array_unshift( $links, $settings_link );
}
return $links;
}

add_action('wp_footer','wpprotect_footer');
add_action('admin_menu','wpprotect_admin');
add_action('wp_head','wpprotect_header');
add_filter('plugin_action_links', 'wpprotect_actions', 10, 2);
?>