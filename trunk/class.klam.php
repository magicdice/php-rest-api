<?php
	require_once('class.restapi.php');

	class Klam extends RestApi
	{
		protected $cache_ext = 'klam';
		protected $api_key;

		function __construct($api_key = false)
		{
			if ($api_key)
				$this->api_key = $api_key;
		}
		function shorten($longurl, $params = array())
		{
			$url = "http://kl.am/api/shorten/";
			$get = array('url' => $longurl);
			$get = array_merge($get, $params);
			if ($this->api_key)
				$get['api_key'] = $this->api_key;

			return $this->request($url, array('get'=>$get,'cache_life'=>0));
		}
		
		function link_stats($shorturl, $group = false)
		{
			$url = "http://kl.am/api/stats/";
			$get = array('short' => $shorturl);
			if ($group == 'url')
				$get['group'] = 'url';
			return $this->request($url, array('get'=>$get));
		}
		
		function account_stats($params = array())
		{
			$url = "http://kl.am/api/stats/";
			$get = $params;
			if ($this->api_key)
				$get['api_key'] = $this->api_key;
			return $this->request($url, array('get'=>$get));
		}
		
	}