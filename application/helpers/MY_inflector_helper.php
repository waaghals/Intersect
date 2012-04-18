<?php
if( ! function_exists('ordinal_suffix'))
{
	function ordinal_suffix($num)
	{
		if($num < 11 || $num > 13)
		{
			switch($num % 10)
			{
				case 1 :
					return $num.'st';
				case 2 :
					return $num.'nd';
				case 3 :
					return $num.'rd';
			}
		}
		return $num.'th';
	}

}