<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller{

	private $data = array();
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('search_model');
	}
	
	public function _remap()
	{
		// Initierar data
		$site			= $this->data['site']			= $this->uri->rsegment(2, 'all');
		$search_string	= $this->data['search_string']	= trim($this->uri->rsegment(3, ''));
			
		// Ser om vi postat utan javascript enablat
		if($this->input->post('submit') !== FALSE)
			$this->submit($site);
			
		// Bygger meny
		$this->data['menu_array'] = array(
			'all' => FALSE,
			'google' => FALSE,
			'yahoo' => FALSE,
			'bing' => FALSE
		);
		
		// Sätter aktivt menyval
		if(array_key_exists($site, $this->data['menu_array']))
			$this->data['menu_array'][$site] = TRUE;
		
		// Gör en sökning om vi har någonting i söksträngen
		if($search_string !== '')
			$this->search($site, $search_string);

		// Echoar ut sökresultaten vi requestar med ajax
		if($this->input->is_ajax_request())
			echo $this->load->view('search_results', $this->data['search_results'], TRUE);
		else
			$this->load->view('search_index', $this->data);
	}
	
	private function search($site, $search_string)
	{
		if(array_key_exists($site, $this->search_model->search_engines) || $site === 'all')
		{
			// URL decodar för urlencodning sker i modellen
			$search_string = urldecode($search_string);
			
			$this->data['search_results']['results'] = ($site === 'all')
				? $this->search_model->get_combined_results($search_string)
				: $this->search_model->get_results($site, $search_string);
		}
		else
		{
			show_error('Sökmotorn existerar inte!');
		}
	}
	
	private function submit($site)
	{
		$search_string = ($this->input->post('search_string') !== FALSE)
			? urlencode($this->input->post('search_string')) : '';
		
		// Redirectar för att URL-strukturen ska bli snygg utan javascript
		redirect("/search/{$site}/{$search_string}");
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/search.php */