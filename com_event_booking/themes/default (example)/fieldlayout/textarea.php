<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2023 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
?>
<textarea name="<?php echo $name; ?>"
		  id="<?php echo $name; ?>"<?php echo $attributes; ?>><?php echo htmlspecialchars((string) $value, ENT_COMPAT, 'UTF-8'); ?></textarea>
