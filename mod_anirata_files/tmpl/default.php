<?php

// No direct access to this file
defined('_JEXEC') or die;

$files_path = JPATH_BASE . '/' . $params->get('files_path');

$files = array_merge(glob($files_path . '/*'), glob($files_path . '/**/*'));

function human_filesize($bytes, $dec = 2): string {
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor == 0) $dec = 0;

    return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
}

echo "

<script>
function filePreview(i)
{

}
</script>

<ul class='list-group'>
";

foreach($files as $i => $file)
{
    if (is_dir($file)) continue;

    $name = substr($file, strlen($files_path) + 1);
    $url = JURI::base() . htmlentities(substr($file, strlen(JPATH_BASE) + 1 ));
    $urlEncoded = urlencode($url);

    $size = human_filesize(filesize($file));

    echo "
<li class='list-group-item'>
    <b>$name ($size)</b>
    <br/>
    <a href='$url' target='_blank'><i class='fa fa-eye'></i> Ouvrir</a>

    <a href='$url' target='_blank' class='ms-3' download><i class='fa fa-download'></i> Télécharger</a>
   
</li>
";

}

echo "
</ul>
";

/*
<div style='display: none'>
 <a target='_blank' class='ms-3' onclick='filePreview($i)'><i class='fa fa-eye'></i> Prévisualiser</a> 
        <iframe loading='lazy' src='https://docs.google.com/viewer?embedded=true&amp;hl=en&amp;url=$urlEncoded'></iframe>
 </div>
 */