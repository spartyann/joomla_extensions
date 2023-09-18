<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2023 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Layout variables
 *
 * @var stdClass                 $item
 * @var Joomla\Registry\Registry $params
 * @var int                      $Itemid
 */

$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
$clearfix        = $bootstrapHelper->getClassMapping('clearfix');
$rowFluid        = $bootstrapHelper->getClassMapping('row-fluid');
$btn             = $bootstrapHelper->getClassMapping('btn');
$iconCalendar    = $bootstrapHelper->getClassMapping('icon-calendar');
$iconMapMaker    = $bootstrapHelper->getClassMapping('icon-map-marker');
$btnPrimary      = $bootstrapHelper->getClassMapping('btn-primary');
$btnBtnPrimary   = $bootstrapHelper->getClassMapping('btn btn-primary');

$config     = EventbookingHelper::getConfig();
$timeFormat = $config->event_time_format ?: 'g:i a';
$dateFormat = $config->date_format;

// Just to be safe in case someone override prepareDisplayData method in wrong way
if (isset($item->cssClasses))
{
	$cssClasses = $item->cssClasses;
}
else
{
	$cssClasses = [];
}

$cssClasses[] = 'eb-event-item-grid-default-layout';
?>
<div class="<?php echo implode(' ', $cssClasses); ?>">
	<?php
	if (!empty($item->thumb_url))
	{
	?>
		<div class="eb-event-thumb-container <?php echo $clearfix; ?>">
			<a href="<?php echo $item->url; ?>"><img src="<?php echo $item->thumb_url; ?>" class="eb-event-thumb" alt="<?php echo $item->image_alt ?: $item->title; ?>"/></a>
			<div class="eb-event-main-category"><?php echo $item->category_name; ?></div>
		</div>
	<?php
	}
	?>
	<div class="eb-event-title-container">
		<a class="eb-event-title" href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a>
	</div>
	<div class="eb-event-date-time">
		<i class="<?php echo $iconCalendar; ?>"></i>
		<?php
		if ($item->event_date != EB_TBC_DATE)
		{
			echo HTMLHelper::_('date', $item->event_date, $dateFormat, null);
		}
		else
		{
			echo Text::_('EB_TBC');
		}

		if (strpos($item->event_date, '00:00:00') === false)
		{
		?>
			<span class="eb-time"><?php echo HTMLHelper::_('date', $item->event_date, $timeFormat, null) ?></span>
		<?php
		}

		if ((int) $item->event_end_date)
		{
			if (strpos($item->event_end_date, '00:00:00') === false)
			{
				$showTime = true;
			}
			else
			{
				$showTime = false;
			}

			$startDate =  HTMLHelper::_('date', $item->event_date, 'Y-m-d', null);
			$endDate   = HTMLHelper::_('date', $item->event_end_date, 'Y-m-d', null);

			if ($startDate == $endDate)
			{
				if ($showTime)
				{
				?>
					-<span class="eb-event-time"><?php echo HTMLHelper::_('date', $item->event_end_date, $timeFormat, null) ?></span>
				<?php
				}
			}
			else
			{
				echo ' - ' . HTMLHelper::_('date', $item->event_end_date, $dateFormat, null);

				if ($showTime)
				{
				?>
					<span class="eb-event-time"><?php echo HTMLHelper::_('date', $item->event_end_date, $timeFormat, null) ?></span>
				<?php
				}
			}
		}
		?>
	</div>

	<?php
	if ($item->location_id)
	{
		?>
		<div class="eb-event-location">
			<i class="<?php echo $iconMapMaker; ?>"></i>
			<?php
			if ((float) $item->lat != 0
				|| (float) $item->long != 0
				|| ($item->location && EventbookingHelper::isValidMessage($item->location->description)))
			{
				?>
				<a href="<?php echo Route::_('index.php?option=com_eventbooking&view=map&location_id=' . $item->location_id . '&Itemid=' . $Itemid . '&tmpl=component'); ?>" class="eb-colorbox-map"><span><?php echo $item->location_name ; ?></span></a>
				<?php
			}
			else
			{
				echo $item->location_name;
			}
			?>
		</div>
		<?php
	}

	if ($item->priceDisplay)
	{
	?>
		<div class="eb-event-price <?php echo $btnBtnPrimary; ?>">
			<span class="eb-individual-price"><?php echo $item->priceDisplay; ?></span>
		</div>
	<?php
	}

	if ($params->get('show_short_description', 1))
	{
	?>
		<div class="eb-event-short-description <?php echo $clearfix; ?>">
			<?php
			if ($params->get('short_description_limit'))
			{
				echo HTMLHelper::_('string.truncate', $item->short_description, (int) $params->get('short_description_limit'));
			}
			else
			{
				echo $item->short_description;
			}
			?>
		</div>
	<?php
	}

	// Event message to tell user that they already registered, need to login to register or don't have permission to register...
	echo EventbookingHelperHtml::loadCommonLayout('common/event_message.php', ['config' => $config, 'event' => $item]);

	if ($params->get('show_register_buttons', 1) && !$item->is_multiple_date)
	{
	?>
		<div class="eb-taskbar <?php echo $clearfix; ?>">
			<ul>
				<?php
				if ($item->can_register)
				{
					echo EventbookingHelperHtml::loadCommonLayout('common/buttons_register.php', ['item' => $item, 'config' => $config, 'Itemid' => $Itemid]);
				}
				elseif ($item->waiting_list && $item->registration_type != 3 && !EventbookingHelperRegistration::isUserJoinedWaitingList($item->id))
				{
					echo EventbookingHelperHtml::loadCommonLayout('common/buttons_waiting_list.php', ['item' => $item, 'config' => $config, 'Itemid' => $Itemid]);
				}
				?>
			</ul>
		</div>
	<?php
	}
	?>
</div>


