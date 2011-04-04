<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_model extends CI_Model {
	
	public $search_engines = array();
	
	function __construct()
	{
		parent::__construct();
		
		// Lägger upp inställningarna i config/search_settings.php
		// och hämtar search_engines arrayen.
		$this->config->load('search_settings');
		$this->search_engines = $this->config->item('search_engines');
	}

	/**
	 * Get combined results from all search engines, sort after occurance and position,
	 * and save it to the database.
	 * @param	string	$search_string
	 * @return	array	$results
	 */
	public function get_combined_results($search_string)
	{
		$point_array = array();

		foreach($this->search_engines as $site => $value)
		{
			// Hämtar sökresultat från varje sökmotor i search_settings.php,
			// fortsätter om resultat hittas
			if(($search_results = $this->get_results($site, $search_string, FALSE)) !== FALSE)
			{
				foreach($search_results as $index => $result)
				{
					// Se om $result['meta'] redan existerar i $point_array, om returnerar $key
					// rarray_search gör en recursive array_search på $point_array @ MY_array_helper.php
					if(($key = rarray_search($result['meta'], $point_array)) !== FALSE)
					{
						// Om $key returnerades, lägg till poäng i $point_array[$key]['points']
						$point_array[(int)$key]['points'] += (10-$index);
					}
					else
					{
						// Om inte $key returnerades lägg till resultatet med poäng i $point_array
						$point_array[] = $result;
						$point_array[(sizeof($point_array)-1)]['points'] = (10-$index);
					}
				}
			}
			else
			{
				return FALSE;
			}
		}
		
		// Sortera efter poäng, sort_by_points @ MY_array_helper.php
		usort($point_array, 'sort_by_points');
		
		// Spara till databasen
		if($this->_save_to_db('all', $search_string, $point_array))
		{
			// Retunerar de 10 högst rankade resultaten direkt från arrayen
			return array_slice($point_array, 0, 10);
		}
		
		return FALSE;
	}
	
	/**
	 * Get the search results from a specific site (search engine).
	 * Save to database is optional.
	 * 
	 * @param	string	$site
	 * @param	string	$search_string
	 * @param	boolean	$save_to_db
	 * @return	array	$results
	 */
	public function get_results($site, $search_string, $save_to_db = TRUE)
	{		
		// Matchar $site mot keysen (sökmotorerna) i $search_engines
		if(array_key_exists($site, $this->search_engines))
		{
			if(($html = $this->_get_engine_html($site, $search_string)) !== FALSE)
			{
				if(preg_match_all($this->search_engines[$site]['regex'], $html, $results, PREG_SET_ORDER))
				{
					// Trimma resultatet
					$results = $this->_clean_results($results);
					
					if($save_to_db)
					{
						if($this->_save_to_db($site, $search_string, $results))
						{
							return $results;
						}
					}
					else
					{
						return $results;
					}
				}
			}
		}
		
		return FALSE;
	}
	
	private function _clean_results(array $results)
	{
		// Måste sätta en limit eftersom sökningen kan ge färre än 10 resultat
		$limit = (sizeof($results) < 10) ? sizeof($results) : 10;
		
		for($i = 0; $i < $limit; $i++)
		{
			$result_array[] = array(
				'title' => trim(strip_tags($results[$i]['title'])),
				'url' => $results[$i]['url'],
				'meta' => strtolower(rtrim(preg_replace('/http[s]?:\/\//', '', $results[$i]['url']), '/')),
				'text' => (isset($results[$i]['text'])) ? trim(strip_tags($results[$i]['text'])) : NULL
			);
		}

		return $result_array;
	}
	
	private function _save_to_db($site, $search_string, array $results)
	{
		// Matchar $site mot keysen (sökmotorerna) i $search_engines
		// och 'all' för kombinerade resultat.
		if(array_key_exists($site, $this->search_engines) || $site === 'all')
		{
			// Förbereder söksträngen för databasen
			$search_string = mysql_real_escape_string($search_string);
			
			if($this->db->insert('searches', array('engine' => $site, 'search' => $search_string)) === FALSE)
				return FALSE;
				
			$search_id = $this->db->insert_id();
			
			for($i = 0; $i < 10; $i++)
			{
				// Ser till att 'search_id' kommer med och 'points' går bort
				$data = array(
					'search_id' => $search_id,
					'title' => $results[$i]['title'],
					'url' => $results[$i]['url'],
					'meta' => $results[$i]['meta'],
					'text' => $results[$i]['text']
				);
				
				if($this->db->insert('results', $data) === FALSE)
					return FALSE;
			}
			
			return TRUE;
		}
	}
	
	/**
	 * Get the html from the search engine (site) using cURL
	 * @param	string	$site
	 * @param	string	$search_string
	 * @return	string	$html
	 */
	private function _get_engine_html($site, $search_string)
	{
		// Matchar $site mot keysen (sökmotorerna) i $search_engines
		if(array_key_exists($site, $this->search_engines))
		{
			// Trimmar och URL-enkodar söksträngen så den beter sig rätt hos sökmotorerna,
			// fortsätter om strängen inte är tom
			if(($search_string = urlencode(trim($search_string))) !== '')
			{
				$site_url = $this->search_engines[$site]['url'];
			
		        $ch = curl_init($site_url . $search_string);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		        $html = curl_exec($ch);
		        curl_close($ch);
		        
				// Konverterar texten till utf-8
		        return utf8_encode($html);
			}
		}
		
		return FALSE;
	}
}

/* End of file welcome.php */
/* Location: ./application/models/search_model.php */