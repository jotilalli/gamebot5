<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * core/MY_Controller.php
 *
 * Default application controller
 *
 */
class Application extends CI_Controller
{

	protected $data = array();   // parameters for view components
	protected $id;  // identifier for our content

	/**
	 * Constructor.
	 * Establish view parameters & load common helpers
	 */

	function __construct()
	{
		parent::__construct();
		$this->data = array();
		$this->data['site-title'] = 'GameBots G5: Assignment 1'; // our default title
		$this->errors = array();
		$this->data['pageTitle'] = 'Welcome';   // our default page
		$this->data['appRoot'] = (strlen(dirname($_SERVER['SCRIPT_NAME'])) === 1 ? "" : dirname($_SERVER['SCRIPT_NAME']));

		/**
		 * Add in additional CSS files used (in the CSS folder) by using
		 * 
		 * 		$this->pageStyles[] = "filename";
		 * 
		 * in the INDIVIDUAL controllers where the filename 
		 * is just the filename WITHOUT the extension
		 */
		$this->pageStyles = array('button', 'smartphone', 'style', 'tablet', 'table');

		/**
		 * Add in additional JS files used (in the JS folder) by using
		 * 
		 * 		$this->pageScripts[] = "filename";
		 * 
		 * in the INDIVIDUAL controllers where the filename 
		 * is just the filename WITHOUT the extension
		 */
		$this->pageScripts = array();

		$this->userSession();
	}

	/**
	 * Render this page
	 */
	function render()
	{
		// This is a workaround to dynamically append a folder name if application is not in root.
		$tempMenu = array();
		foreach ($this->config->item('menu_choices')['menuname'] as $record)
		{
			$record['appRoot'] = $this->data['appRoot'];
			$tempMenu['menuname'][] = $record;
		}
		$tempMenu['userSession'] = $this->data['userSession'];


		$this->data['menubar'] = $this->parser->parse('_menubar', $tempMenu, true);


		$this->data['loadScripts'] = "";
		if (!empty($this->pageScripts))
		{
			$scripts = array();
			foreach ($this->pageScripts as $js)
			{
				$temp['appRoot'] = $this->data['appRoot'];
				$temp['filename'] = $js;
				$scripts['scripts'][] = $temp;
			}

			$this->data['loadScripts'] = $this->parser->parse('__js', $scripts, true);
		}

		$this->data['loadStyles'] = "";
		if (!empty($this->pageStyles))
		{
			$styles = array();
			foreach ($this->pageStyles as $css)
			{
				$temp['appRoot'] = $this->data['appRoot'];
				$temp['filename'] = $css;
				$styles['styles'][] = $temp;
			}
			$this->data['loadStyles'] = $this->parser->parse('__css', $styles, true);
		}

		$this->data['content'] = $this->parser->parse($this->data['pagebody'], $this->data, true);
		// finally, build the browser page!
		$this->data['data'] = &$this->data;
		$this->parser->parse('_template', $this->data);
	}

	function userSession()
	{
		$display = $this->load->view('_loginForm', '', true);
		if (is_null($this->session->username))
		{
			// No username set in session
			if (!is_null($this->input->post('login')))
			{
				// login button clicked
				$username = $this->input->post('username');
				if ($username != "")
				{
					$this->session->username = $username;
					$player['player'] = $this->session->username;
					if (!$this->players->exists($username))
					{
						$this->players->add($player);
					}
					$display = $this->parser->parse('_loggedIn', $player, true);
				}
			}
		} else
		{
			// Username in session
			if (!is_null($this->input->post('logout')))
			{
				// logout button clicked
				unset($_SESSION['username']);
				$display = $this->load->view('_loginForm', '', true);
			} else
			{

				$player['player'] = $this->session->username;
				$display = $this->parser->parse('_loggedIn', $player, true);
			}
		}

		$this->data['userSession'] = $display;
	}

}

/* End of file MY_Controller.php */
/* Location: application/core/MY_Controller.php */
