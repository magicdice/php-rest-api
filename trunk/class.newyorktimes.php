<?php
	require_once('class.restapi.php');

	class NewYorkTimes extends RestApi
	{
		protected $api_key;
		protected $url_base;
		protected $cache_ext = 'nyt';
		
		function __construct($api_key)
		{
			$this->api_key = $api_key;
			parent::__construct();
		}
	}
	
	class ArticleSearch extends NewYorkTimes
	{
		function __construct($api_key)
		{
			$this->url_base = "http://api.nytimes.com/svc/search/v1/article";
			parent::__construct($api_key);
		}
		
		function request($url, $extra = array())
		{
			$api_key = array('api-key' => $this->api_key);
			if (isset($extra['get']) && is_array($extra['get']))
				$extra['get'] = array_merge($extra['get'], $api_key);
			else
				$extra['get'] = $api_key;
			return parent::request($url, $extra);
		}
		
		/*
			Available parameters:
				begin_date: YYYYMMDD
				end_date: YYYYMMDD
				rank: "newest" (default), "oldest", or "closest"
				... and more
		*/
		function search($query, $params = array())
		{
			$url = $this->url_base;
			$get['query'] = $query;
			$get = array_merge($get, $params);
			return $this->request($url, array('get'=>$get));
		}
	}