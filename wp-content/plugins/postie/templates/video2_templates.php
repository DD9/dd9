<?php

$small = '<object ' .
        'classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ' .
        'codebase="http://www.apple.com/qtactivex/qtplugin.cab" ' .
        'width="128"' . 'height="96"> ' .
        '<param name="src" value="{FILELINK}" /> ' .
        '<param name="autoplay" value="no" /> ' .
        '<param name="controller" value="true" /> ' .
        '<embed src="{FILELINK}" ' .
        'width="128" height="96"' .
        'autoplay="no" controller="true" ' .
        'type="video/quicktime" ' .
        'pluginspage="http://www.apple.com/quicktime/download/" ' .
        'width="128" height="110">' .
        '</embed> ' .
        '</object>';
$medium = '<object ' .
        'classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ' .
        'codebase="http://www.apple.com/qtactivex/qtplugin.cab" ' .
        'width="320"' . 'height="240"> ' .
        '<param name="src" value="{FILELINK}" /> ' .
        '<param name="autoplay" value="no" /> ' .
        '<param name="controller" value="true" /> ' .
        '<embed src="{FILELINK}" ' .
        'width="320" height="240"' .
        'autoplay="no" controller="true" ' .
        'type="video/quicktime" ' .
        'pluginspage="http://www.apple.com/quicktime/download/" ' .
        'width="320" height="260">' .
        '</embed> ' .
        '</object>';
$medium_widescreen = '<object ' .
        'classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ' .
        'codebase="http://www.apple.com/qtactivex/qtplugin.cab" ' .
        'width="480"' . 'height="270"> ' .
        '<param name="src" value="{FILELINK}" /> ' .
        '<param name="autoplay" value="no" /> ' .
        '<param name="controller" value="true" /> ' .
        '<embed src="{FILELINK}" ' .
        'width="480" height="270"' .
        'autoplay="no" controller="true" ' .
        'type="video/quicktime" ' .
        'pluginspage="http://www.apple.com/quicktime/download/" ' .
        'width="480" height="290">' .
        '</embed> ' .
        '</object>';
$large = '<object ' .
        'classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ' .
        'codebase="http://www.apple.com/qtactivex/qtplugin.cab" ' .
        'width="480"' . 'height="360"> ' .
        '<param name="src" value="{FILELINK}" /> ' .
        '<param name="autoplay" value="no" /> ' .
        '<param name="controller" value="true" /> ' .
        '<embed src="{FILELINK}" ' .
        'width="480" height="360"' .
        'autoplay="no" controller="true" ' .
        'type="video/quicktime" ' .
        'pluginspage="http://www.apple.com/quicktime/download/" ' .
        'width="480" height="380">' .
        '</embed> ' .
        '</object>';
$large_widescreen = '<object ' .
        'classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ' .
        'codebase="http://www.apple.com/qtactivex/qtplugin.cab" ' .
        'width="640"' . 'height="360"> ' .
        '<param name="src" value="{FILELINK}" /> ' .
        '<param name="autoplay" value="no" /> ' .
        '<param name="controller" value="true" /> ' .
        '<embed src="{FILELINK}" ' .
        'width="640" height="360"' .
        'autoplay="no" controller="true" ' .
        'type="video/quicktime" ' .
        'pluginspage="http://www.apple.com/quicktime/download/" ' .
        'width="640" height="380">' .
        '</embed> ' .
        '</object>';
$flv_embed = '[flv:{FILELINK} 480 270]';

$simple_link = '<a href="{FILELINK}">{FILENAME}</a>';

$custom = isset($config) ? (array_key_exists('VIDEO2TEMPLATE', $config) ? $config['VIDEO2TEMPLATE'] : "") : "";
$video2Templates = compact('simple_link', 'small', 'medium', 'medium_widescreen', 'large', 'large_widescreen', 'flv_embed', 'custom');
?>
