<?php
/*
Plugin Name: Autotune
Plugin URI: 
Version: 0.0
Description: Autotune
Author: Tom Haraldseid
Author URI: 
Plugin Slug: autotune
Icon: icon-fab-update
*/
 

 
class Autotune extends Plugin {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');
		
		define('MY_PLUGIN_URL', site_url().'plugin/autotune/');
		define('MY_PLUGIN_PATH', PLUGINSPATH.'autotune/');

		
        
        $this->lang->load($_SESSION['language']['name'], $_SESSION['language']['name']);
        
	}

	public function index()
	{
		

		
		
		
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.cust.min.js', 'comment'=>'create utilities'));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.resize.min.js', 'comment'=>'create utilities'));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.fillbetween.min.js', 'comment'=>'create utilities'));
		
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.orderBar.min.js', 'comment'=>'create utilities'));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.pie.min.js', 'comment'=>'create utilities'));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.time.min.js', 'comment'=>'create utilities'));
		
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.tooltip.min.js', 'comment'=>'create utilities'));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/flot/jquery.flot.axislabels.js', 'comment'=>'create utilities'));
		
		
		
		$css_in_page = $this->load->view('css', '', TRUE);
		$this->layout->add_css_in_page(array('data'=> $css_in_page, 'comment' => ''));
		
		$js_in_page = $this->load->view('js', '', TRUE);
		$this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => ''));

		$this->layout->view('index');
	
	}
}

?>