<?php
	require_once('class.restapi.php');
	
	class Twitter extends OAuthRestApi
	{
		protected $cache_ext = 'twitter';
		protected $endpoint = 'https://api.twitter.com/1.1';
		protected $encodepost = true;
		
		function getAuthorizeUrl($callback = false)
		{
			return parent::getAuthorizeUrl("https://api.twitter.com/oauth/request_token", "https://twitter.com/oauth/authorize", $callback);
		}
		
		function getAccessToken()
		{
			return parent::getAccessToken("https://api.twitter.com/oauth/access_token");
		}
		
		function setCurlOpts($ch)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		    parent::setCurlOpts($ch);
		}

		/********* STATUS METHODS *********/
		
		function public_timeline()
		{
			$url = "{$this->endpoint}/statuses/public_timeline.{$this->format}";
			return $this->request($url);
		}
		
		/*
			Available parameters:
				since: HTTP formatted date
				since_id: twitter status ID
				count: integer up to 200
				page: integer
		*/
		function friends_timeline($params = array())
		{
			$url = "{$this->endpoint}/statuses/friends_timeline.{$this->format}";
			if (count($params) > 0)
				return $this->request($url, array('get'=>$params));
			else
				return $this->request($url);
		}
		
		/*
			Available parameters:
				since: HTTP formatted date
				since_id: twitter status ID
				count: integer up to 200
				page: integer
		*/
		function user_timeline($id = false, $params = array())
		{
	        if ($id === false)
	            $url = "{$this->endpoint}/statuses/user_timeline.{$this->format}";
	        else
			{
				$id = urlencode($id);
	            $url = "{$this->endpoint}/statuses/user_timeline/$id.{$this->format}";
			}
			if (count($params) > 0)
				return $this->request($url, array('get'=>$params));
			else
				return $this->request($url);
		}
		
		function show_status($id)
		{
			$url = "{$this->endpoint}/statuses/show/$id.{$this->format}";
			return $this->request($url);
		}
		
		function update_status($status, $reply_to = false)
		{
			$url = "{$this->endpoint}/statuses/update.{$this->format}";
			$post = array('status' => $status);
			if ($reply_to !== false)
				$post['in_reply_to_status_id'] = $reply_to;
			return $this->request($url, array('post'=>$post));
		}
		
		/*
			Available parameters:
				since: HTTP formatted date
				since_id: twitter status ID
				page: integer
		*/
		function replies($params = array())
		{
			$url = "{$this->endpoint}/statuses/replies.{$this->format}";
			if (count($params) > 0)
				return $this->request($url, array('get'=>$params));
			else
				return $this->request($url);
		}
		
		function mentions($params = array())
		{
			$url = "{$this->endpoint}/statuses/mentions.{$this->format}";
			if (count($params) > 0)
				return $this->request($url, array('get'=>$params));
			else
				return $this->request($url);
		}
		
		function destroy_status($id)
		{
			$url = "{$this->endpoint}/statuses/destroy/$id.{$this->format}";
			$post = array('id' => $id);
			return $this->request($url, array('post'=>$post));
		}
		
		
		/********* USER METHODS *********/
		
		function friends($id = false, $page = false, $cursor = false)
		{
	        if ($id === false)
	            $url = "{$this->endpoint}/statuses/friends.{$this->format}";
	        else
			{
				$id = urlencode($id);
	            $url = "{$this->endpoint}/statuses/friends/$id.{$this->format}";
			}
			if ($page !== false && $page > 1)
				return $this->request($url, array('get'=>array('page'=>$page)));
			elseif ($cursor !== false)
				return $this->request($url, array('get'=>array('cursor'=>$cursor)));
			else
				return $this->request($url);
		}
		
		function followers($id = false, $page = false, $cursor = false)
		{
	        if ($id === false)
	            $url = "{$this->endpoint}/statuses/followers.{$this->format}";
	        else
			{
				$id = urlencode($id);
	            $url = "{$this->endpoint}/statuses/followers/$id.{$this->format}";
			}
			if ($page !== false && $page > 1)
				return $this->request($url, array('get'=>array('page'=>$page)));
			elseif ($cursor !== false)
				return $this->request($url, array('get'=>array('cursor'=>$cursor)));
			else
				return $this->request($url);
		}
		
		function show_user($id)
		{
			$id = urlencode($id);
			$url = "{$this->endpoint}/users/show/{$id}.{$this->format}";
			return $this->request($url);
		}
		
		
		/********* DIRECT MESSAGE METHODS *********/
		
		/*
			Available parameters:
				since: HTTP formatted date
				since_id: twitter status ID
				page: integer
		*/
		function direct_messages($params = array())
		{
			$url = "{$this->endpoint}/direct_messages.{$this->format}";
			if (count($params) > 0)
				return $this->request($url, array('get'=>$params));
			else
				return $this->request($url);
		}
		
		/*
			Available parameters:
				since: HTTP formatted date
				since_id: twitter status ID
				page: integer
		*/
		function sent_direct_messages($params = array())
		{
			$url = "{$this->endpoint}/direct_messages/sent.{$this->format}";
			if (count($params) > 0)
				return $this->request($url, array('get'=>$params));
			else
				return $this->request($url);
		}
		
		function new_direct_message($user, $text)
		{
			$url = "{$this->endpoint}/direct_messages/new.{$this->format}";
			$post = array('user' => $user, 'text' => $text);
			return $this->request($url, array('post'=>$post));
		}
		
		function destroy_direct_message($id)
		{
			$url = "{$this->endpoint}/direct_messages/destroy/$id.{$this->format}";
			$post = array('id' => $id);
			return $this->request($url, array('post'=>$post));
		}
		
		
		/********* FRIENDSHIP METHODS *********/

		function create_friendship($id, $follow = false)
		{
			$id = urlencode($id);
			$url = "{$this->endpoint}/friendships/create/$id.{$this->format}";
			$post = array();
			if ($follow)
				$post['follow'] = 'true';
			return $this->request($url, array('post'=>$post), true);
		}

		function destroy_friendship($id)
		{
			$id = urlencode($id);
			$url = "{$this->endpoint}/friendships/destroy/$id.{$this->format}";
			return $this->request($url, array(), true);
		}
		
		function exists($id1, $id2)
		{
			$url = "{$this->endpoint}/friendships/exists.{$this->format}";
			$get = array('user_a' => $id1, 'user_b' => $id2);
			return $this->request($url, array('get'=>$get));
		}


		/********* SOCIAL GRAPH METHODS *********/
		
		function friend_ids($id = false)
		{
	        if ($id === false)
	            $url = "{$this->endpoint}/friends/ids.{$this->format}";
	        else
			{
				$id = urlencode($id);
	            $url = "{$this->endpoint}/friends/ids/$id.{$this->format}";
			}
			return $this->request($url);
		}
		
		function follower_ids($id = false)
		{
	        if ($id === false)
	            $url = "{$this->endpoint}/followers/ids.{$this->format}";
	        else
			{
				$id = urlencode($id);
	            $url = "{$this->endpoint}/followers/ids/$id.{$this->format}";
			}
			return $this->request($url);
		}
		
		
		/********* ACCOUNT METHODS *********/
		function rate_limit_status()
		{
			$url = "{$this->endpoint}/account/rate_limit_status.{$this->format}";
			return $this->request($url);
		}
		
		function verify_credentials()
		{
			$url = "{$this->endpoint}/account/verify_credentials.{$this->format}";
			return $this->request($url);
		}
		
		/********* FAVORITE METHODS *********/
		function favorites($id = false, $page = false)
		{
	        if ($id === false)
	            $url = "{$this->endpoint}/favorites.{$this->format}";
	        else
			{
				$id = urlencode($id);
	            $url = "{$this->endpoint}/favorites/$id.{$this->format}";
			}
			if ($page !== false && $page > 1)
				return $this->request($url, array('get'=>array('page'=>$page)));
			else
				return $this->request($url);
		}
		
		/********* NOTIFICATION METHODS *********/
		function follow($id)
		{
			$url = "{$this->endpoint}/notifications/follow/{$id}.{$this->format}";
			return $this->request($url, array(), true);
		}
		function leave($id)
		{
			$url = "{$this->endpoint}/notifications/leave/{$id}.{$this->format}";
			return $this->request($url, array(), true);
		}

		/********* SEARCH METHODS *********/
		function search($query, $params = array())
		{
			$url = "{$this->endpoint}/search/tweets.{$this->format}";
			$get = array('q' => $query);
			$get = array_merge($get, $params);
			return $this->request($url, array('get'=>$get));
		}

		
	}
