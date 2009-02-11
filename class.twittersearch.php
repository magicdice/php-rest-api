<?php
	class TwitterSearch extends RestApi
	{
		protected $cache_ext = 'twittersearch';
		
		/*
			Available parameters:
				lang: ISO 639-1 language code
				rpp: integer up to 100 (results per page)
				page: integer
				since_id: twitter status ID
				geocode: latitude,longitude,radius (radius in mi or km: e.g. 10mi)
				show_user: set to "true" to add username to tweet
		*/
		function search($query, $params = array())
		{
			$url = "http://search.twitter.com/search.{$this->format}";
			$get = array('q' => urlencode($query));
			$get = array_merge($get, $params);
			return $this->request($url, array('get'=>$get));
		}
	}