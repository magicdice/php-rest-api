<?php
	class RightScale extends RestApi
	{
		protected $api_version = '1.0';
		protected $url_base;
		protected $cache_ext = 'rs';
		protected $format = 'js';
		protected $account;
		protected $cache_life;
		protected $get_location = false;
		protected $location;
		
		function __construct($account, $username, $password)
		{
			$this->account = $account;
			$this->login($username, $password);
			$this->url_base = "https://my.rightscale.com/api/acct/{$this->account}/";
			parent::__construct();
		}
		
		function request($url, $extra = array())
		{
			$url = $this->url_base . $url;
			if (!isset($extra['headers']))
				$extra['headers'] = array();
			$extra['headers'][] = 'X-API-VERSION: ' . $this->api_version;
			return parent::request($url, $extra);
		}
		
		function setCurlOpts($ch)
		{
			parent::setCurlOpts($ch);
			if ($this->get_location == true)
			{
				curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$this,'getLocation'));
			}
		}
		
		function getLocation($ch, $header)
		{
			if (preg_match('/^Location: (.+)$/', $header, $m))
				$this->location = $m[1];

			return strlen($header);
		}
		
		/****** SERVER METHODS *******/
		function getServers()
		{
			$url = "servers.{$this->format}";
			return $this->request($url);
		}
		function getServer($id)
		{
			$url = "servers/$id.{$this->format}";
			return $this->request($url);
		}
		function runScript($server_id, $script_id)
		{
			$tmp = $this->get_location;
			$this->get_location = true;
			$url = "servers/$server_id/run_script";
			$post = array('right_script'=>$this->url_base."right_scripts/$script_id");
			$this->request($url, array('post'=>$post));
			$this->get_location = $tmp;
			$result = $this->location;
			$this->location = null;
			return $result;
		}

		function getDeployments()
		{
			$url = "deployments.{$this->format}";
			return $this->request($url);
		}
		function getDeployment($id)
		{
			$url = "deployments/$id.{$this->format}";
			return $this->request($url);
		}
		function getRightScripts()
		{
			$url = "right_scripts.{$this->format}";
			return $this->request($url);
		}
		function getRightScript($id)
		{
			$url = "right_scripts/$id.{$this->format}";
			return $this->request($url);
		}
		
		function getStatus($id)
		{
			$url = "statuses/$id.{$this->format}";
			return $this->request($url);
		}
	}