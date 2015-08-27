<?php

namespace Messenger\Sms\Alpha;

/*
Copyright (c) 2009-2010 by Dmitry Skachko, AlphaSMS.com.ua
All rights reserved.

History:

Version 1.0 (10.06.2009)
 - First Release
 
Version 1.1 (24.09.2009)
 - Added send_date parameter
 - Added wap-push link
 - Added flash message 
 
Version 1.2 (31.10.2009)
- send_date format changed to DATE_ISO8601 - ISO-8601 (example: 2005-08-15T15:52:01+0000)
- added function hasErrors() to check number of errors
- added ability to auto-check for new version of PHP class

Version 1.3 (25.11.2009)
- fixed wrong time format, if no time set
- API moved to alphasms.com.ua
- class renamed

Version 1.4 (02.02.2010)
- Added choice to use HTTPS POST instead of HTTP GET (via variable $mode)

Version 1.5 (08.02.2010)
- Added function to convert text to translit

Version 1.6 (10.03.2010)
- Added function getResponse 
- Now server returs more data that can be accessed as array via new function

Version 1.7 (11.05.2010)
- Send datetime parameter for sendSMS function now can accept timestamp and date in text format 

Version 1.8 (5.06.2011)
- auth API key 

Version 1.9 (19.07.2012)
- Ability to delete a message from server queue if it is not sent yet

*/

class Client
{
	public $mode = 'HTTPS'; //HTTP or HTTPS
	protected $_server = '://alphasms.com.ua/api/http.php';
	protected $_errors = array();
	protected $_last_response = array();
	private $_version = '1.9';
	
	
	//IN: login and password or key on platform (AlphaSMS)
	public function __construct($login = '', $password = '', $key = '')
	{
		$this->_login = $login;
		$this->_password = $password;
		$this->_key = $key;
	}

	//IN: 	sender name, phone of receiver, text message in UTF-8 - if long - will be auto split
	//		send_dt - date-time of sms sending, wap - url for Wap-Push link, flash - for Flash sms.
	//OUT: 	message_id to track delivery status, if empty message_id - check errors via $this->getErrors()
	public function sendSMS($from, $to, $message, $send_dt = 0, $wap = '', $flash = 0)
	{
		if(!$send_dt)
			$send_dt = date('Y-m-d H:i:s');
		$d = is_numeric($send_dt) ? $send_dt : strtotime($send_dt);
		$data = array(	'from'=>$from,
						'to'=>$to,
						'message'=>$message,
						'ask_date'=>date(DATE_ISO8601, $d),
						'wap'=>$wap,
						'flash'=>$flash,
						'class_version'=>$this->_version);
		$result = $this->execute('send', $data);
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['id'];
	}
	
	//IN: 	message_id to track delivery status
	//OUT: 	text name of status
	public function receiveSMS($sms_id)
	{
		$data = array('id'=>$sms_id);
		$result = $this->execute('receive', $data);
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['status'];		
	}

	//IN: 	message_id to delete
	//OUT: 	text name of status
	public function deleteSMS($sms_id)
	{
		$data = array('id'=>$sms_id);
		$result = $this->execute('delete', $data);
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['status'];		
	}
	
	//OUT:	amount in UAH, if no return - check errors
	public function getBalance()
	{
		$result = $this->execute('balance');
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['balance'];		
	}
	
	//OUT:	returns number of errors
	public function hasErrors()
	{
		return count($this->_errors);
	}
	
	//OUT:	returns array of errors
	public function getErrors()
	{
		return $this->_errors;
	}

	public function getResponse()
	{
		return $this->_last_response;
	}

	public function translit($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',    'є' => 'ye', 
			'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',   'і' => 'i', 
			'и' => 'i',   'й' => 'j',   'к' => 'k',   'ї' => 'yi', 
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'kh',   'ц' => 'ts',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shch',
			'ь' => '\'',  'ы' => 'y',   'ъ' => '"',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
			
			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',   'Є' => 'Ye',
			'Ё' => 'Yo',   'Ж' => 'Zh',  'З' => 'Z',   'І' => 'I',
			'И' => 'I',   'Й' => 'J',   'К' => 'K',   'Ї' => 'Yi',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'Kh',   'Ц' => 'Ts',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Shch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '"',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);
		$result = strtr($string, $converter);
		
		//upper case if needed
		if(mb_strtoupper($string) == $string)
			$result = mb_strtoupper($result);
			
		return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $result);
	}	

	protected function execute($command, $params = array())
	{
		$this->_errors = array();

		//HTTP GET
		if(strtolower($this->mode) == 'http')
		{
			$response = @file_get_contents($this->generateUrl($command, $params));
			return @unserialize($this->base64_url_decode($response));
		}
		else
		{
			$params['login'] = $this->_login;
			$params['password'] = $this->_password;
			$params['key'] = $this->_key;
			$params['command'] = $command;
			$params_url = '';
			foreach($params as $key=>$value)
		 		$params_url .= '&' . $key . '=' . $this->base64_url_encode($value);
		
			//cURL HTTPS POST
			$ch = curl_init(strtolower($this->mode) . $this->_server);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_POST, count($params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params_url);			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			
			$response = @curl_exec($ch);
			curl_close($ch);

			$this->_last_response = @unserialize($this->base64_url_decode($response));
			return $this->_last_response;		
		}
	}
	
	protected function generateUrl($command, $params = array())
	{
		$params_url = '';
		if(count($params))
			foreach($params as $key=>$value)
		 		$params_url .= '&' . $key . '=' . $this->base64_url_encode($value);
		if(!$this->_key) { 		
			$auth = '?login=' . $this->base64_url_encode($this->_login) . '&password=' . $this->base64_url_encode($this->_password);
		} else {
			$auth = '?key=' . $this->base64_url_encode($this->_key);
		}
		$command = '&command=' . $this->base64_url_encode($command);
		return strtolower($this->mode) . $this->_server . $auth . $command . $params_url;
	}

	public function base64_url_encode($input)
	{
		return strtr(base64_encode($input), '+/=', '-_,');
	}
	
	public function base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_,', '+/='));
	}

}
