<?php
/**
 * @license    MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;

$document = Factory::getDocument();
$document->addStyleDeclaration("
.af-over-gray>.list-group-item:hover {
	background-color: #f8f8f8;
}
");

require ModuleHelper::getLayoutPath('mod_anirata_files', $params->get('layout', 'default'));
