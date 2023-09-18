<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2023 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper   = $this->bootstrapHelper;
$config            = $this->config;

$return            = base64_encode(Uri::getInstance()->toString());
$timeFormat        = $config->event_time_format ?: 'g:i a';
$dateFormat        = $config->date_format;

if (!$config->get('show_register_buttons', 1))
{
	$hideRegisterButtons = true;
}
else
{
	$hideRegisterButtons = false;
}

$rowFluid     = $bootstrapHelper->getClassMapping('row-fluid');
$span8        = $bootstrapHelper->getClassMapping('span8');
$span4        = $bootstrapHelper->getClassMapping('span4');
$btn          = $bootstrapHelper->getClassMapping('btn');
$btnInverse   = $bootstrapHelper->getClassMapping('btn-inverse');
$btnPrimary   = $bootstrapHelper->getClassMapping('btn-primary');
$iconCalendar = $bootstrapHelper->getClassMapping('icon-calendar');
$iconMapMaker = $bootstrapHelper->getClassMapping('icon-map-marker');
$clearfix     = $bootstrapHelper->getClassMapping('clearfix');

$monthNamesShort = [
	Text::_('JANUARY_SHORT'),
	Text::_('FEBRUARY_SHORT'),
	Text::_('MARCH_SHORT'),
	Text::_('APRIL_SHORT'),
	Text::_('MAY_SHORT'),
	Text::_('JUNE_SHORT'),
	Text::_('JULY_SHORT'),
	Text::_('AUGUST_SHORT'),
	Text::_('SEPTEMBER_SHORT'),
	Text::_('OCTOBER_SHORT'),
	Text::_('NOVEMBER_SHORT'),
	Text::_('DECEMBER_SHORT'),
];
?>
<div id="eb-events" class="eb-events-timeline">
	<?php
		for ($i = 0 , $n = count($this->items) ;  $i < $n ; $i++)
		{
			$event = $this->items[$i];

			$layoutData = [
				'item'                => $event,
				'isMultipleDate'      => $event->is_multiple_date,
				'showInviteFriend'    => false,
				'Itemid'              => $this->Itemid,
				'return'              => $return,
				'hideRegisterButtons' => $hideRegisterButtons,
			];

			$registerButtons = EventbookingHelperHtml::loadCommonLayout('common/buttons.php', $layoutData);

			$cssClasses = $event->cssClasses ?? [];
			$cssClasses[] = 'eb-event-container';
			?>
            <div class="<?php echo implode(' ', $cssClasses); ?>">
                <div class="eb-event-date-container">
                    <div class="eb-event-date <?php echo $btnInverse; ?>">
                        <?php
							if ($event->event_date != EB_TBC_DATE)
							{
								$monthNumber = HTMLHelper::_('date', $event->event_date, 'n', null);

							?>
                                <div class="eb-event-date-day">
                                    <?php echo HTMLHelper::_('date', $event->event_date, 'd', null); ?>
                                </div>
                                <div class="eb-event-date-month">
                                    <?php echo $monthNamesShort[$monthNumber - 1]; ?>
                                </div>
                                <div class="eb-event-date-year">
                                    <?php echo HTMLHelper::_('date', $event->event_date, 'Y', null); ?>
                                </div>
                            <?php
							}
							else
							{
								echo Text::_('EB_TBC');
							}
						?>
                    </div>
                </div>
                <h2 class="eb-even-title-container">
                    <?php
						if ($config->hide_detail_button !== '1')
						{
						?>
                            <a class="eb-event-title" href="<?php echo $event->url; ?>"><?php echo $event->title; ?></a>
                        <?php
						}
						else
						{
							echo $event->title;
						}
					?>
                </h2>
                <div class="eb-event-information <?php echo $rowFluid; ?>">
                    <div class="<?php echo $span8; ?>">
                        <div class="eb-event-date-info <?php echo $clearfix; ?>">
                            <i class="<?php echo $iconCalendar; ?>"></i>
                            <?php
								if ($event->event_date != EB_TBC_DATE)
								{
									echo HTMLHelper::_('date', $event->event_date, $dateFormat, null);
								}
								else
								{
									echo Text::_('EB_TBC');
								}

								if (strpos($event->event_date, '00:00:00') === false)
								{
								?>
                                    <span class="eb-time"><?php echo HTMLHelper::_('date', $event->event_date, $timeFormat, null) ?></span>
                                <?php
								}

                                if ((int) $event->event_end_date)
								{
									if (strpos($event->event_end_date, '00:00:00') === false)
									{
										$showTime = true;
									}
									else
									{
										$showTime = false;
									}

									$startDate =  HTMLHelper::_('date', $event->event_date, 'Y-m-d', null);
									$endDate   = HTMLHelper::_('date', $event->event_end_date, 'Y-m-d', null);

									if ($startDate == $endDate)
									{
										if ($showTime)
										{
										?>
                                            -<span class="eb-time"><?php echo HTMLHelper::_('date', $event->event_end_date, $timeFormat, null) ?></span>
                                        <?php
										}
									}
									else
									{
										echo ' - ' . HTMLHelper::_('date', $event->event_end_date, $dateFormat, null);

										if ($showTime)
										{
										?>
                                            <span class="eb-time"><?php echo HTMLHelper::_('date', $event->event_end_date, $timeFormat, null) ?></span>
                                        <?php
										}
									}
								}
							?>
                        </div>
                        <?php
							if ($event->location_id)
							{
							?>
                            <div class="<?php echo $clearfix; ?>">
                                <i class="<?php echo $iconMapMaker; ?>"></i>
                                <?php
                                    if ((float) $event->lat != 0
	                                    || (float) $event->long != 0
	                                    || ($event->location && EventbookingHelper::isValidMessage($event->location->description)))
									{
										$location = $event->location;

										if ($location->image || EventbookingHelper::isValidMessage($location->description))
										{
										?>
                                            <a href="<?php echo Route::_('index.php?option=com_eventbooking&view=map&location_id=' . $event->location_id . '&Itemid=' . $this->Itemid); ?>"><span><?php echo $event->location_name ; ?></span></a>
                                        <?php
										}
										else
										{
										?>
                                            <a href="<?php echo Route::_('index.php?option=com_eventbooking&view=map&location_id=' . $event->location_id . '&tmpl=component&Itemid=' . $this->Itemid); ?>" class="eb-colorbox-map"><span><?php echo $event->location_name ; ?></span></a>
                                        <?php
										}
									}
									else
									{
										echo $event->location_name;
									}
								?>
                            </div>
                            <?php
							}
						?>
                    </div>
                    <?php
					if ($event->priceDisplay)
					{
					?>
                        <div class="<?php echo $span4; ?>">
                            <div class="eb-event-price-container <?php echo $btnPrimary; ?>">
                                <span class="eb-individual-price"><?php echo $event->priceDisplay; ?></span>
                            </div>
                        </div>
                    <?php
					}
					?>
                </div>
                <?php
					if (in_array($config->get('register_buttons_position', 0), [1, 2]))
					{
					?>
                        <div class="eb-taskbar eb-register-buttons-top <?php echo $clearfix; ?>">
                            <ul>
                                <?php
									echo $registerButtons;

									if ($config->hide_detail_button !== '1' || $event->is_multiple_date)
									{
									?>
                                        <li>
                                            <a class="<?php echo $btn; ?>" href="<?php echo $event->url; ?>">
                                                <?php echo $event->is_multiple_date ? Text::_('EB_CHOOSE_DATE_LOCATION') : Text::_('EB_DETAILS'); ?>
                                            </a>
                                        </li>
                                    <?php
									}
								?>
                            </ul>
                        </div>
                    <?php
					}
				?>
                <div class="eb-description-details <?php echo $clearfix; ?>">
                    <?php
						if (!empty($event->thumb_url))
						{
						?>
							<a href="<?php echo $event->url; ?>"><img src="<?php echo $event->thumb_url; ?>" class="eb-thumb-left" alt="<?php echo $event->image_alt ?: $event->title; ?>"/></a>
						<?php
						}

						echo $event->short_description;
					?>
                </div>
                <?php
					if ($config->display_ticket_types && !empty($event->ticketTypes))
					{
						echo EventbookingHelperHtml::loadCommonLayout('common/tickettypes.php', ['ticketTypes' => $event->ticketTypes, 'config' => $config, 'event' => $event]);
					?>
                        <div class="<?php echo $clearfix; ?>"></div>
                    <?php
					}

					// Event message to tell user that they already registered, need to login to register or don't have permission to register...
					echo EventbookingHelperHtml::loadCommonLayout('common/event_message.php', ['config' => $config, 'event' => $event]);

					if (in_array($config->get('register_buttons_position', 0), [0, 2]))
					{
					?>
                        <div class="eb-taskbar eb-register-buttons-bottom <?php echo $clearfix; ?>">
                            <ul>
                                <?php
								echo $registerButtons;

								if ($config->hide_detail_button !== '1' || $event->is_multiple_date)
								{
								?>
                                    <li>
                                        <a class="<?php echo $btn; ?>" href="<?php echo $event->url; ?>">
                                            <?php echo $event->is_multiple_date ? Text::_('EB_CHOOSE_DATE_LOCATION') : Text::_('EB_DETAILS'); ?>
                                        </a>
                                    </li>
                                <?php
								}
								?>
                            </ul>
                        </div>
                    <?php
					}
				?>
            </div>
		<?php
		}
	?>
</div>
<?php

// Add Google Structured Data
PluginHelper::importPlugin('eventbooking');
Factory::getApplication()->triggerEvent('onDisplayEvents', [$this->items]);
