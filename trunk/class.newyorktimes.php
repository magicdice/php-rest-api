<?php
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
		
		function search($query, $start = false, $end = false, $rank = false)
		{
			$url = $this->url_base;
			$get = array('query' => urlencode($query));
			if ($start !== false)
				$get['begin_date'] = $start;
			if ($end !== false)
				$get['end_date'] = $end;
			if ($rank !== false)
				$get['rank'] = $rank;
			return $this->request($url, array('get'=>$get));
		}
	}