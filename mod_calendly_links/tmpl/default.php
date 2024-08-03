<?php

// No direct access to this file

use Joomla\Module\CalendlyLinks\Site\ModCalendlyLinksHelper;
use Joomla\CMS\Date\Date;

defined('_JEXEC') or die;

/*
assigned_to => Yann Tassy
event_type_uuid => 0ac5e8de-9e3c-40e7-a5ae-f5955ad566de
event_type_name => Séance de Reiki à distance
event_start_time => 2023-11-23T13:30:00+01:00
event_end_time => 2023-11-23T14:30:00+01:00
invitee_uuid => 4f39dd6f-d18e-42a9-843d-4e5863332d1c
invitee_first_name => Yann
invitee_last_name => Tassy
invitee_email => tassy.yann@gmail.com
answer_1 => +33 6 51 37 63 20
*/

//
// **************** Calendly
// Exemple: https://www.yanntassy.fr/calendly-confirmation?assigned_to=Yann%20Tassy&event_type_uuid=0ac5e8de-9e3c-40e7-a5ae-f5955ad566de&event_type_name=S%C3%A9ance%20de%20Reiki%20%C3%A0%20distance&event_start_time=2023-11-23T13%3A30%3A00%2B01%3A00&event_end_time=2023-11-23T14%3A30%3A00%2B01%3A00&invitee_uuid=4f39dd6f-d18e-42a9-843d-4e5863332d1c&invitee_first_name=Yann&invitee_last_name=Tassy&invitee_email=tassy.yann%40gmail.com&answer_1=%2B33%206%2051%2037%2063%2020
// Meet: https://calendly.com/events/a425cf70-af23-48d0-85ad-5f205ffb52f6/google_meet
// Cancel: https://calendly.com/cancellations/4f39dd6f-d18e-42a9-843d-4e5863332d1c
// Reschedule: https://calendly.com/reschedulings/4f39dd6f-d18e-42a9-843d-4e5863332d1c
//



$assigned_to = $_GET['assigned_to'] ?? null;
$event_type_uuid = $_GET['event_type_uuid'] ?? null;
$event_type_name = $_GET['event_type_name'] ?? null;
$event_start_time = $_GET['event_start_time'] ?? null;
$event_end_time = $_GET['event_end_time'] ?? null;
$invitee_uuid = $_GET['invitee_uuid'] ?? null;
$invitee_first_name = $_GET['invitee_first_name'] ?? null;
$invitee_last_name = $_GET['invitee_last_name'] ?? null;
$invitee_email = $_GET['invitee_email'] ?? null;
$answer_1 = $_GET['answer_1'] ?? null;


if ($invitee_uuid == null) return;

// Security
if (ModCalendlyLinksHelper::isValidUUID($invitee_uuid) == false || ModCalendlyLinksHelper::isValidUUID($event_type_uuid) == false) return;

$cancelLink = 'https://calendly.com/cancellations/' . $invitee_uuid;
$rescheduleLink = 'https://calendly.com/reschedulings/' . $invitee_uuid;


$dateText = "";
try {
	if (ModCalendlyLinksHelper::isValidCalendlyDate($event_start_time))
	{
		//2023-11-23T13:30:00+01:00

		$date = new \JDate($event_start_time);
		$dateText .= 'Rendez-vous le: ';
		$dateText .= '<b>' . $date->format('l d F Y à H:i') . '</b>';
		$dateText .= '<br/>';
	}
} catch (\Throwable $ex)
{
}

?>

<p>
	<?php echo $dateText; ?>
	
	Si vous souhaitez <b>annuler</b> le rendez-vous <a target="_blank" href="<?php echo htmlentities($cancelLink);?>">cliquez sur ce lien</a>.
	<br/>
	Si vous souhaitez <b>décaler</b> le rendez-vous <a target="_blank" href="<?php echo htmlentities($rescheduleLink);?>">cliquez sur ce lien</a>.
</p>
