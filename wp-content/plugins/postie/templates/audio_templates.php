<?php

$google_reader = '<embed type="application/x-shockwave-flash" ' .
        'src="http://www.google.com/reader/ui/3247397568-audio-player.swf?audioUrl={FILELINK}" ' .
        'width="400" height="27" allowscriptaccess="never" quality="best" ' .
        'bgcolor="#ffffff" wmode="window" flashvars="playerMode=embedded" />';

$simple_link = '<a href="{FILELINK}">{FILENAME}</a>';

$custom = isset($config) ? (array_key_exists('AUDIOTEMPLATE', $config) ? $config['AUDIOTEMPLATE'] : "") : "";
$audioTemplates = compact('google_reader', 'simple_link', 'custom');
?>
