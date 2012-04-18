<?php
if( ! function_exists('timeframe'))
{
	function timeframe($datetime)
	{

		$from = strtotime($datetime);
		$elapsed = time() - $from;

		if($elapsed < 3)
		{
			return 'Just now';
		}

		$timeframes = array(
					10 * 12 * 30 * 24 * 60 * 60 => 'decade', 
					12 * 30 * 24 * 60 * 60 => 'year', 
					30 * 24 * 60 * 60 => 'month', 
					24 * 60 * 60 => 'day', 
					60 * 60 => 'hour', 
					60 => 'minute', 
					1 => 'second');

		foreach($timeframes as $secs => $str)
		{
			$diff = $elapsed / $secs;
			if($diff >= 1)
			{
				$r = round($diff);
				return $r . ' ' . $str . ($r > 1 ? 's' : '');
			}
		}
	}
}