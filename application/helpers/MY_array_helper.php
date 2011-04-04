<?php

/**
 * 
 * Search for needle in a recursive array
 * @author http://www.php.net/manual/en/function.array-search.php#97645
 * 
 * @param $haystack
 * @param $needle
 * @param $index
 */
function rarray_search($needle, $haystack, $index = null)
{
	$aIt	= new RecursiveArrayIterator($haystack);
	$it		= new RecursiveIteratorIterator($aIt);
	
	// Tar bort ".www" om det finns för bättre jämföring
	$needle = preg_replace('/\bwww./', '', $needle);
   
	while($it->valid())
    {
    	// Tar bort ".www" om det finns för bättre jämföring
    	$current = preg_replace('/\bwww./', '', $it->current());

		if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($current == $needle))
		{
			return $aIt->key();
		}
		$it->next();
	}

	return FALSE;
}


/**
 * 
 * En sorteringsfunktion till usort, sorterar efter 'points'
 * @param $x
 * @param $y
 */
function sort_by_points($x, $y)
{
	if ($x['points'] == $y['points'])
		return 0;
	else
		return ($x['points'] > $y['points']) ? -1 : 1;
}