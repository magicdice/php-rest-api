<?php
	require_once('class.restapi.php');

	class LastFM extends RestApi
	{
		protected $cache_ext = 'fm';
		protected $endpoint = 'http://ws.audioscrobbler.com/2.0/';
		protected $api_key;

		function __construct($api_key)
		{
			$this->api_key = $api_key;
		}
		
		function request($method, $request_type = 'get', $extra = array())
		{
			$params = array(
				'api_key' => $this->api_key,
				'method' => $method,
				'format' => $this->format
			);

			if ($request_type!='post')
				$request_type = 'get';
			if (isset($extra[$request_type]) && is_array($extra[$request_type]))
				$extra[$request_type] = array_merge($extra[$request_type], $params);
			else
				$extra[$request_type] = $params;
			
			return parent::request($this->endpoint, $extra);
		}
		
		function user_getRecentTracks($user, $limit = false)
		{
			$extra = array('get'=>array('user'=>$user));
			if ($limit)
				$extra['get']['limit'] = $limit;
			$result =  $this->request('user.getRecentTracks', 'get', $extra);
			if (is_object($result) && is_array($result->recenttracks->track))
				return $result->recenttracks->track;
			else
				return array();
		}
		
		function user_getTopTracks($user, $period = false)
		{
			$extra = array('get'=>array('user'=>$user));
			if ($period)
				$extra['get']['period'] = $period;
			$result = $this->request('user.getTopTracks', 'get', $extra);
			if (is_object($result) && is_array($result->toptracks->track))
				return $result->toptracks->track;
			else
				return array();
		}
	}
	
	class LastFMTrack
	{
		protected $obj;
		
		function __construct($obj)
		{
			$this->obj = $obj;
		}
		
		function getArtist()
		{
			if (isset($this->obj->artist->{'#text'}))
				return $this->obj->artist->{'#text'};
			elseif (isset($this->obj->artist->name))
				return $this->obj->artist->name;
			else
				return false;
		}
		
		function getAlbum()
		{
			if (isset($this->obj->album->{'#text'}))
				return $this->obj->album->{'#text'};
			else
				return false;
		}
		
		function getTrack()
		{
			if (isset($this->obj->name))
				return $this->obj->name;
			else
				return false;
		}
		
		function getImageSrc($size = 'small')
		{
			if (is_array($this->obj->image))
			{
				foreach ($this->obj->image as $img)
				{
					if ($img->size == $size)
						return $img->{'#text'};
				}
			}
			return false;
		}
		
		function getImage($size = 'small', $default = false)
		{
			$src = $this->getImageSrc($size);
			if ($src || $default)
			{
				if (!$src && $default)
					$src = $default;
				switch ($size)
				{
					case 'small':
						$px = 34;
						break;
					case 'medium':
						$px = 64;
						break;
					case 'large':
						$px = 126;
						break;
					default:
						$px = false;
				}
				if ($px)
					$dim = "width=\"$px\" height=\"px\" ";
				else
					$dim = "";
				return "<img src=\"$src\" $dim/>";
			}
			else
				return false;
		}
		
		function getDate($format)
		{
			if (isset($this->obj->date->uts))
				return date($format, $this->obj->date->uts);
			else
				return false;
		}
		
		function getUrl()
		{
			if (isset($this->obj->url))
				return $this->obj->url;
			else
				return false;
		}
		
		function getArtistUrl()
		{
			$track_url = $this->getUrl();
			if ($track_url)
			{
				$artist_url = preg_replace('|/[^/]*/[^/]*$|', '', $track_url);
				if ($artist_url && $artist_url!=$track_url)
					return $artist_url;
			}
			return false;
		}
		function nowPlaying()
		{
			if (isset($this->obj->{'@attr'}->nowplaying) && $this->obj->{'@attr'}->nowplaying=='true')
				return true;
			else
				return false;
		}
	}