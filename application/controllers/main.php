<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	private $search_engines = array();
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('search_model');
	}
	
	public function search()
	{
		$site = $data['site'] = $this->uri->segment(2, 'all');
		$search_string = $data['search_string'] = urldecode($this->uri->segment(3, ''));
		
		// Bygger meny
		$data['menu_array'] = array('all' => '', 'google' => '', 'yahoo' => '', 'bing' => '');
		if(array_key_exists($site, $data['menu_array']))
			$data['menu_array'][$site] = 'class="active" ';
		
		if(array_key_exists($site, $this->search_model->search_engines) || $site === 'all')
		{
			if(trim($search_string) !== '')
			{
				$data['search_results']['results'] = ($site === 'all')
					? $this->search_model->get_combined_results($search_string)
					: $this->search_model->get_results($site, $search_string);
			}
		}
		
		// Echoar ut sökresultaten vi postar med ajax
		if($this->input->post('ajax'))
			echo $this->load->view('search_results', $data['search_results'], TRUE);
		else
			$this->load->view('search_index', $data);
	}
	
	public function submit()
	{
		$site = $this->uri->segment(3, 'all');
		$search_string = ($this->input->post('search_string') !== FALSE)
			? urlencode($this->input->post('search_string')) : '';

		redirect("/search/{$site}/{$search_string}");
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/search.php */