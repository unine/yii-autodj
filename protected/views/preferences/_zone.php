<ul class="zone-panel">
<?php
switch($time_zone)
{
	case 'Africa':
		$tz = DateTimeZone::AFRICA;
		break;
	case 'America':
		$tz = DateTimeZone::AMERICA;
		break;
	case 'Antarctica':
		$tz = DateTimeZone::ANTARCTICA;
		break;
	case 'Asia':
		$tz = DateTimeZone::ASIA;
		break;
	case 'Atlantic':
		$tz = DateTimeZone::ATLANTIC;
		break;
	case 'Australia':
		$tz = DateTimeZone::AUSTRALIA;
		break;
	case 'Europe':
		$tz = DateTimeZone::EUROPE;
		break;
	case 'Indian':
		$tz = DateTimeZone::INDIAN;
		break;
	case 'Pacific':
		$tz = DateTimeZone::PACIFIC;
		break;
}
$alltime = DateTimeZone::listIdentifiers($tz);
foreach($alltime as $item):
?>
<li><?php echo CHtml::link($item,'#'.$item,array('class'=>'settimezone'));?></li>
<?php endforeach;?>
</ul>
