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
.af-over-gray img { display: inline-block; }
.af-over-gray .td { display: table-cell; vertical-align: top;}
.af-over-gray .td-100 { display: table-cell; vertical-align: top; width: 100%; }
.af-over-gray .tbl-100 { display: table; width: 100%; }
.af-over-gray .white-space-nowrap { white-space: nowrap!important; }
");

require ModuleHelper::getLayoutPath('mod_tassy_files', $params->get('layout', 'default'));
