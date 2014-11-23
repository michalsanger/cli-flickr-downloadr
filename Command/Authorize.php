<?php

namespace FlickrDownloadr\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Authorize extends Command
{
	const URL_PATH_REQUEST_TOKEN = 'request_token';
    const URL_PATH_AUTHORIZE = 'authorize';
    const URL_PATH_ACCESS_TOKEN = 'access_token';
    const AUTHORIZE_PERMS = 'read';

	/** @var string */
	private $baseUrl;
	
	/** @var string */
	private $consumerKey;

	/** @var string */
	private $consumerSecret;
	
    /** @var \FlickrDownloadr\Oauth\ClientFactory */
    private $oauthClientFactory;

	/**
	 * @param string $baseUrl
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param \FlickrDownloadr\Oauth\ClientFactory $oauthClientFactory
	 */
    function __construct($baseUrl, $consumerKey, $consumerSecret, \FlickrDownloadr\Oauth\ClientFactory $oauthClientFactory)
    {
		$this->baseUrl = $baseUrl;
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->oauthClientFactory = $oauthClientFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('authorize')
            ->setDescription('Authorize this application, obtain a users OAuth token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->printWelcome($output);
        list($requestToken, $requestTokenSecret) = $this->getRequestToken();
        $pinCode = $this->getPinCode($input, $output, $requestToken);
        $oauthResponse = $this->getAccessToken($pinCode, $requestToken, $requestTokenSecret);
        $this->saveToConfig($output, $oauthResponse['oauth_token'], $oauthResponse['oauth_token_secret']);
    }
    
    private function printWelcome(OutputInterface $output)
    {
        $output->getFormatter()->setStyle('heading', new OutputFormatterStyle('green', null, array('bold')));
        $output->getFormatter()->setStyle('bold', new OutputFormatterStyle(null, null, array('bold')));
        
        $output->writeln('<heading>Flickr Downloadr Authorization</heading>');
        $output->writeln('<info>This script runs the OAuth flow in out-of-band mode. You will need access to</info>');
        $output->writeln('<info>a web browser to authorise the application.</info>');
        $output->writeln('');
        $output->writeln('<info>At the end of this script credentials will be saved into config file.</info>');
        $output->writeln('');
    }
    
	/**
	 * 
	 * @return array userToken, userTokenSecret
	 * @throws \Exception
	 */
    private function getRequestToken()
    {
		$oauthClient = $this->oauthClientFactory->createInstance();
		$options = [
			'query' => [
				'oauth_callback' => 'oob'
			]
		];
		$res = $oauthClient->get(self::URL_PATH_REQUEST_TOKEN, $options);
		$code = (int)$res->getStatusCode();
		$body = $res->getBody()->getContents();
		parse_str($body, $resp);
        if ($code != 200) {
            throw new \Exception('There was an error communicating with Flickr. ' . $this->oauthClient->response['response']);
        }
        if ($resp['oauth_callback_confirmed'] !== "true") {
            throw new \Exception('The callback was not confirmed by Flickr.');
        }
		return array(
			$resp['oauth_token'],
			$resp['oauth_token_secret'],
		);
    }
    
    private function getPinCode(InputInterface $input, OutputInterface $output, $requestToken)
    {
        $output->writeln('<info>Copy and paste this URL into your web browser and follow the prompts to get a pin code:</info>');
        $urlQuery = http_build_query(['oauth_token' => $requestToken, 'perms' => self::AUTHORIZE_PERMS]);
        $authUrl = $this->baseUrl . self::URL_PATH_AUTHORIZE . '?' . $urlQuery;
        $output->writeln('<bold>' . $authUrl . '</bold>');
        
        $helper = $this->getHelper('question');
        $question = new Question('<heading>What was the Pin Code?</heading>: ');
        $pinCode = $helper->ask($input, $output, $question);
        return $pinCode;
    }
    
    /**
     * @param string $pinCode
     * @param string $requestToken
     * @param string $requestTokenSecret
     * @return array
     * @throws \Exception
     */
    private function getAccessToken($pinCode, $requestToken, $requestTokenSecret)
    {
		$options = [
			'query' => [
				'oauth_verifier' => $pinCode,
				'oauth_token' => $requestToken,
			]
		];
		$oauthClient = $this->oauthClientFactory->createInstance($requestToken, $requestTokenSecret);
		$res = $oauthClient->get(self::URL_PATH_ACCESS_TOKEN, $options);
		$code = (int)$res->getStatusCode();
		$resp = [];
		parse_str($res->getBody()->getContents(), $resp);
		
        if ($code != 200) {
            throw new \Exception('There was an error communicating with Flickr. ' 
                . $this->oauthClient->response['response']);
        }
        return $resp;
    }
    
    private function saveToConfig(OutputInterface $output, $token, $tokenSecret)
    {
        $oauth = array();
        $oauth['key'] = $this->consumerKey;
        $oauth['secret'] = $this->consumerSecret;
        $oauth['token'] = $token;
        $oauth['tokenSecret'] = $tokenSecret;
        $conf = array('parameters' => array('oauth' => $oauth));
        
        $confFilename = $_SERVER['HOME'] . '/.flickrDownloadr.neon';
        $neonEncoder = new \Nette\Neon\Encoder();
        $confEncoded = $neonEncoder->encode($conf, 1);
        if (file_put_contents($confFilename, $confEncoded) === FALSE) {
            throw new \Exception('Error saving file: ' . $confFilename);
        }
        if (chmod($confFilename, 0600) === FALSE) {
            throw new \Exception('Error setting file permisions');
        }
        $output->writeln('<info>Authorization OK, credentials saved into:</info>');
        $output->writeln($confFilename);
        $output->writeln('');
    }
}
