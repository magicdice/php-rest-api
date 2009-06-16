<?php
	require_once('class.restapi.php');

	class Flickr extends RestApi
	{
		protected $cache_ext = 'flickr';
		protected $api_key;
		protected $format = 'json';
		
		function __construct($api_key)
		{
			$this->api_key = $api_key;
			parent::__construct();
		}
		
		function request($method, $extra = array())
		{
			$url = "http://api.flickr.com/services/rest/";
			$get = array(
				'method' => $method,
				'api_key' => $this->api_key,
				'format' => $this->format
			);
			if ($this->format == 'json')
				$get['nojsoncallback'] = 1;

			if (isset($extra['get']) && is_array($extra['get']))
				$extra['get'] = array_merge($extra['get'], $get);
			
			return parent::request($url, $extra);
		}

		function requestFeed($method, $extra = array())
		{
			$url = "http://api.flickr.com/services/feeds/$method.gne";
			$get = array(
				'lang' => 'en-us',
				'format' => $this->format
			);
			if ($this->format == 'json')
				$get['nojsoncallback'] = 1;

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
			$get = array(
				'user_id' => $user_id,
				'extras' => 'date_upload,date_taken,owner_name'
			);
			$get = array_merge($get, $params);
			return $this->request($method, array('get'=>$get));
		}
		
		function photos_search($params = array())
		{
			$method = 'flickr.photos.search';
			$get = array(
				'extras' => 'date_upload,date_taken,owner_name'
			);
			$get = array_merge($get, $params);
			return $this->request($method, array('get'=>$get));
		}

		function favorites_getPublicList($user_id, $params = array())
		{
			$method = 'flickr.favorites.getPublicList';
			$get = array(
				'user_id' => $user_id,
				'extras' => 'date_upload,date_taken,owner_name'
			);
			$get = array_merge($get, $params);
			return $this->request($method, array('get'=>$get));
		}
		
		/* ids, tags, tagmode */
		function feed_public($user_id, $params = array())
		{
			$method = 'photos_public';
			$get = array('id' => $user_id);
			$get = array_merge($get, $params);
			return $this->requestFeed($method, array('get'=>$get));
		}
	}

	class Photo
	{
		var	$id;
		var	$secret;
		var	$server;
		var	$farm;
		var	$size;
		var	$owner;
		var	$title;
		var	$ownername;

		function Photo($data)
		{
			$columns = array('id', 'secret', 'server', 'farm', 'owner',	'title', 'ownername', 'description');
			foreach	($columns as $column)
			{
				if (is_object($data))
				{
					if (!isset($data->{$column}))
						continue;
					$this->{$column} = $data->{$column};
				}
				else
				{
					if (!isset($data[$column]))
						continue;
					if (is_array($data[$column]))
						$this->{$column} = $data[$column]['_content'];
					else
						$this->{$column} = $data[$column];
				}
			}
		}

		function getImg($size =	'')	// mstb
		{
			if (in_array($size,	array('s', 't',	'm', 'b')))
					$this->size	= "_$size";
			else
					$this->size	= "";
			$url = "http://farm{$this->farm}.static.flickr.com/{$this->server}/{$this->id}_{$this->secret}{$this->size}.jpg";
			return $url;
		}

		function getUrl($user = false)
		{
			if (!$user)
				$user = $this->owner;
			$url = "http://www.flickr.com/photos/{$user}/{$this->id}/";
			return $url;
		}

		function getAlt()
		{
			$alt = str_replace('"',	'\"', $this->title)	. '	by ' . str_replace('"',	'\"', $this->ownername);
			return $alt;
		}

}
