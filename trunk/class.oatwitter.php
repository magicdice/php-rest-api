<?php
	require_once('class.twitter.php');
	require_once('class.oauth.php');
	
	class OATwitter extends Twitter
	{
		private $oa_method;
		private $consumer;
		private $request_token;
		private $access_token;
		protected $endpoint = 'https://twitter.com';
		
		function __construct($consumer_key = false, $consumer_secret = false)
		{
			if ($consumer_key && $consumer_secret)
			    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
			elseif (defined('TWITTER_CONSUMER_KEY') && defined('TWITTER_CONSUMER_SECRET'))
			    $this->consumer = new OAuthConsumer(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
			else
				return false;
			
		    $this->oa_method = new OAuthSignatureMethod_HMAC_SHA1();
		
			parent::__construct();
		}
		
		function login($oauth_token, $oauth_token_secret)
		{
			$this->access_token = new OAuthConsumer($oauth_token, $oauth_token_secret);
		}
		
		static function parseToken($string)
		{
			$token = array();
			parse_str($string, $token);
			if (isset($token['oauth_token']) && isset($token['oauth_token_secret']))
				return new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
			else
				return false;
		}
		
		function getAuthorizeUrl($callback = false)
		{
			$url = "https://twitter.com/oauth/request_token";
			$req = OAuthRequest::from_consumer_and_token($this->consumer, null, 'GET', $url);
		    $req->sign_request($this->oa_method, $this->consumer, null);
		
			$format = $this->format;
			$this->format = "text";
			$result = $this->request($req->to_url(), array('cache_life'=>0));
			$this->format = $format;
			
			if (($token = self::parseToken($result))===false)
				return false;
			
			$this->request_token = $token;
			$_SESSION['request_token'] = $token;
			
			$url = "https://twitter.com/oauth/authorize";
			$url .= "?oauth_token={$this->request_token->key}";
			if ($callback)
				$url .= "&oauth_callback=" . urlencode($callback);
			return $url;
		}
		
		function getAccessToken()
		{
			if (!is_object($this->request_token))
			{
				if (is_object($_SESSION['request_token']))
					$this->request_token = $_SESSION['request_token'];
				else
					return false;
			}
			$url = "https://twitter.com/oauth/access_token";
			$req = OAuthRequest::from_consumer_and_token($this->consumer, $this->request_token, 'GET', $url);
			$req->sign_request($this->oa_method, $this->consumer, $this->request_token);
		
			$format = $this->format;
			$this->format = "text";
			$result = $this->request($req->to_url(), array('cache_life'=>0));
			$this->format = $format;
			
			if (($token = self::parseToken($result))===false)
				return false;
			return $this->access_token = $token;
		}
		
		function setCurlOpts($ch)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		    parent::setCurlOpts($ch);
		}
		
		function request($url, $extra = array(), $force_post = false)
		{
			$oauth = array(
				'oauth_version' => OAuthRequest::$version,
				'oauth_nonce' => OAuthRequest::generate_nonce(),
				'oauth_timestamp' => OAuthRequest::generate_timestamp(),
				'oauth_consumer_key' => $this->consumer->key,
				'oauth_token' => $this->access_token->key,
				'oauth_signature_method'=>$this->oa_method->get_name()
			);
			
			if (isset($extra['post']))
				$params = $extra['post'];
			elseif (isset($extra['get']))
				$params = $extra['get'];
			else
				$params = array();
			
			if (isset($extra['post']) || $force_post)
				$method = 'POST';
			else
				$method = 'GET';
			
			$params = array_merge($params, $oauth);
			$request = new OAuthRequest($method, $url, $params);
		    $params['oauth_signature'] = $request->build_signature($this->oa_method, $this->consumer, $this->access_token);

			$extra[strtolower($method)] = $params;
		    
			return parent::request($url, $extra, $force_post);
		}
	}