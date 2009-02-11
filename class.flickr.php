<?php
	class Flickr extends RestApi
	{
		protected $cache_ext = 'flickr';
		protected $api_key;
		protected $format = 'php_serial';
		
		function __construct($api_key)
		{
			$this->api_key = $api_key;
			parent::__construct();
		}
		
		function request($method, $extra = array())
		{
			$url = "http://api.flickr.com/services/rest/";
			$get = array('method' => $method, 'api_key' => $this->api_key, 'format' => $this->format);
			
			if (isset($extra['get']) && is_array($extra['get']))
				$extra['get'] = array_merge($extra['get'], $get);
			
			return parent::request($url, $extra);
		}
		
		/*
			$user_id must be NSID
			Available parameters:
				per_page: integer up to 500 (default: 100)
				page: integer
				extras: comma delimited list of any of the following:
					license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media
		*/
		function people_getPublicPhotos($user_id, $params = array())
		{
			$method = 'flickr.people.getPublicPhotos';
			$get = array('user_id' => $user_id);
			$get = array_merge($get, $params);
			return $this->request($method, array('get'=>$get));
		}
		
	}