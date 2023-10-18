<?php

// No direct access to this file
defined('_JEXEC') or die;
use Joomla\Module\AnirataFiles\Site\ModAnirataHelper;
use Joomla\CMS\Date\Date;

$files = ModAnirataHelper::getFiles($params);
$displayModifiedDate = $params->get('display_modified_date') == "1";
$displayCompact = $params->get('display_compact') == "1";

?>

<ul class='list-group af-over-gray'>

<?php
foreach($files as $i => $file)
{
    $name = $file['name'];
    $url = $file['url'];
    $urlEncoded = $file['urlEncoded'];
    $size = $file['size'];
    $createdDate = $file['created_date'];
    $modifiedDate = $file['modified_date'];

	$jCreatedDate = $file['j_created_date'];
    $jModifiedDate = $file['j_modified_date'];

	if ($displayCompact)
	{
?>
    <li class='list-group-item'>
        <a href='<?= $url ?>' class="text-dark" target='_blank'>
			<b><?= $name ?></b>
		</a>
        <a href='<?= $url ?>' target='_blank'><i class='fa fa-eye'></i></a>
        <a href='<?= $url ?>' target='_blank' class='ms-1' download><i class='fa fa-download'></i></a>

        <?php if ($displayModifiedDate === true) { ?>
            <small class="float-end"><?php echo $jModifiedDate->format('d M y'); ?></small>
            
        <?php } ?>
    </li>
<?php
	}
	else
	{
?>

    <li class='list-group-item'>
		<a href='<?= $url ?>' class="text-dark" target='_blank'>
			<b><?= $name ?></b>
		</a>
        <br/>
        <?php if ($displayModifiedDate === true) { ?>
            <small class="float-end"><?= $size ?> - <?php echo $jModifiedDate->format('d M Y - H:i'); ?></small>
            
        <?php } ?>
        <a href='<?= $url ?>' target='_blank'><i class='fa fa-eye'></i> Ouvrir</a>
        <a href='<?= $url ?>' target='_blank' class='ms-3' download><i class='fa fa-download'></i> Télécharger</a>
    </li>

<?php
	}
}
?>

</ul>


<!--
<div style='display: none'>
 <a target='_blank' class='ms-3' onclick='filePreview($i)'><i class='fa fa-eye'></i> Prévisualiser</a> 
        <iframe loading='lazy' src='https://docs.google.com/viewer?embedded=true&amp;hl=en&amp;url=$urlEncoded'></iframe>
 </div>
 -->