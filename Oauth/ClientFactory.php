<?php

namespace FlickrDownloadr\Oauth;

class ClientFactory
{
	/** @var string */
	private $baseUrl;
	
	/** @var string */
	private $key;
	
	/** @var string */
	private $secret;
	
	/**
	 * @param string $baseUrl
	 * @param string $key
	 * @param string $secret
	 */
	function __construct($baseUrl, $key, $secret)
	{
		$this->baseUrl = $baseUrl;
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * @param string $token
	 * @param string $tokenSecret
	 * @return GuzzleHttp\Client
	 */
	public function createInstance($token = NULL, $tokenSecret = NULL)
	{
		$oauthClient = new \GuzzleHttp\Client([
			'base_url' => $this->baseUrl,
			'defaults' => array('auth' => 'oauth'),
		]);
		
		$params = array(
			'consumer_key'    => $this->key,
			'consumer_secret' => $this->secret,			
		);

		if ($token !== NULL) {
			$params['token'] = $token;
		}
		if ($tokenSecret !== NULL) {
			$params['token_secret'] = $tokenSecret;
		}

		$oauth = new \GuzzleHttp\Subscriber\Oauth\Oauth1($params);
		$oauthClient->getEmitter()->attach($oauth);
		return $oauthClient;
	}
}
