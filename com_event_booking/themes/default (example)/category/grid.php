<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2023 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

Factory::getApplication()->getDocument()->addStyleSheet(
	Uri::root(true) . '/media/com_eventbooking/assets/css/eventgrid.min.css',
	['version' => EventbookingHelper::getInstalledVersion()]
);

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
$rowFluid        = $bootstrapHelper->getClassMapping('row-fluid');
$clearfix        = $bootstrapHelper->getClassMapping('clearfix');

if ($this->isDirectMenuLink && $this->introText)
{
	$description = $this->introText;
}
else
{
	$description = $this->category ? $this->category->description: $this->introText;
}
?>
<div id="eb-category-grid" class="eb-container">
	<?php
	if ($this->params->get('show_page_heading', 1))
	{
		if ($this->input->getInt('hmvc_call'))
		{
			$hTag = 'h2';
		}
		else
		{
			$hTag = 'h1';
		}
	?>
        <<?php echo $hTag; ?> class="eb-page-heading">
			<?php
			echo $this->escape($this->params->get('page_heading'));

			if ($this->config->get('enable_ics_export'))
			{
				echo EventbookingHelperHtml::loadCommonLayout('common/ics_export.php');
			}
			?>
        </<?php echo $hTag; ?>>
	<?php
	}

	if ($description)
	{
	?>
		<div class="eb-leading-description <?php echo $clearfix; ?>">
			<?php
			if (!empty($this->category->image) && file_exists(JPATH_ROOT . '/images/com_eventbooking/categories/thumb/' . basename($this->category->image)))
			{
				$rootUri = Uri::root(true);
			?>
				<a href="<?php echo $rootUri . '/' . $this->category->image; ?>" class="eb-modal" target="_blank"><img src="<?php echo $rootUri . '/images/com_eventbooking/categories/thumb/' . basename($this->category->image); ?>" alt="<?php echo $this->category->image_alt ?: $this->category->name; ?>" class="eb-thumb-left" /></a>
			<?php
			}

			echo $description;
			?>
		</div>
	<?php
	}

	if (count($this->categories))
	{
		echo $this->loadCommonLayout('categories/default_items.php');
	}
	
	if ($this->config->get('show_search_bar', 0))
	{
		echo $this->loadCommonLayout('common/search_filters.php');
	}

	if (count($this->items))
	{
		echo $this->loadCommonLayout('common/events_grid_layout.php');
	}
	else
	{
	?>
		<p class="text-info"><?php echo Text::_('EB_NO_EVENTS') ?></p>
	<?php
	}

	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo Route::_('index.php?option=com_eventbooking&view=upcomingevents&layout=grid&Itemid=' . $this->Itemid); ?>">
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="task" value="" />
	</form>
</div>