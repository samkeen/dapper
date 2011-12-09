<?php

namespace thundercats;

class Util
{
	public static function arr_extract_key_pattern($pattern, $src_array, $trim_pattern = TRUE)
	{
		$extracted = array();
		foreach ($src_array as $key => $value)
		{
			if (preg_match($pattern, $key, $match))
			{
				if($trim_pattern)
				{
					$key = substr($key,strlen($match[0]));
				}
				$extracted[$key] = $value;
			}
		}
		return $extracted;
	}
}