<?php
	class NikePlus extends RestApi
	{
		protected $cache_ext = 'nike';
		protected $format = 'xml';
		protected $cache_life = -1;
		
		function getRunList($user_id)
		{
			$url = "http://nikeplus.nike.com/nikeplus/v1/services/widget/get_public_run_list.jsp?userID=" . $user_id;
			return $this->request($url);
		}
		
		function getRun($user_id, $run_id)
		{
			$url = "http://nikeplus.nike.com/nikeplus/v1/services/widget/get_public_run.jsp?id=$run_id&userID=$user_id";
			return $this->request($url);
		}
	}