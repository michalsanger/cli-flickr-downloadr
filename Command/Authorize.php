<?php

namespace FlickrDownloadr\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;


class Authorize extends Command
{
    const CONSUMER_KEY = '3365341effaf533f4fe95f6629a2c9a8';
    const CONSUMER_SECRET = '9c21dac1df1c16a3';
    const URL_REQUEST_TOKEN = 'https://www.flickr.com/services/oauth/request_token';
    const URL_AUTHORIZE = 'https://www.flickr.com/services/oauth/authorize';
    const URL_ACCESS_TOKEN = 'https://www.flickr.com/services/oauth/access_token';
    const AUTHORIZE_PERMS = 'read';

    /**
     * @var \tmhOAuth
     */
    private $oauthClient;

    protected function configure()
    {
        $this
            ->setName('authorize')
            ->setDescription('Authorize this application, obtain a users OAuth token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->printWelcome($output);
        $this->oauthClient = $this->getOauthClient();
        $this->getRequestToken();
        $pinCode = $this->getPinCode($input, $output);
        $oauthResponse = $this->getAccessToken($pinCode);
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
     * @return \tmhOAuth
     */
    private function getOauthClient()
    {
        $oauth = new \tmhOAuth([
            'consumer_key' => self::CONSUMER_KEY,
            'consumer_secret' => self::CONSUMER_SECRET,
        ]);
        return $oauth;
    }
    
    private function getRequestToken()
    {
        $code = $this->oauthClient->apponly_request([
            'without_bearer' => true,
            'method' => 'POST',
            'url' => self::URL_REQUEST_TOKEN,
            'params' => [
                'oauth_callback' => 'oob'
            ]
        ]);
        if ($code != 200) {
            throw new \Exception('There was an error communicating with Flickr. ' 
                . $this->oauthClient->response['response']);
        }
        $resp = $this->oauthClient->extract_params($this->oauthClient->response['response']);
        if ($resp['oauth_callback_confirmed'] !== "true") {
            throw new \Exception('The callback was not confirmed by Flickr.');
        }
        $this->oauthClient->config['user_token'] = $resp['oauth_token'];
        $this->oauthClient->config['user_secret'] = $resp['oauth_token_secret'];
    }
    
    private function getPinCode(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Copy and paste this URL into your web browser and follow the prompts to get a pin code:</info>');
        $urlQuery = http_build_query(['oauth_token' => $this->oauthClient->config['user_token'], 'perms' => self::AUTHORIZE_PERMS]);
        $authUrl = self::URL_AUTHORIZE . '?' . $urlQuery;
        $output->writeln('<bold>' . $authUrl . '</bold>');
        
        $helper = $this->getHelper('question');
        $question = new Question('<heading>What was the Pin Code?</heading>: ');
        $pinCode = $helper->ask($input, $output, $question);
        return $pinCode;
    }
    
    /**
     * @param string $pinCode
     * @return array
     * @throws \Exception
     */
    private function getAccessToken($pinCode)
    {
        $code = $this->oauthClient->user_request([
            'method' => 'POST',
            'url' => self::URL_ACCESS_TOKEN,
            'params' => [
                'oauth_verifier' => $pinCode,
                'oauth_token' => $this->oauthClient->config['user_token'],
            ],
        ]);
        if ($code != 200) {
            throw new \Exception('There was an error communicating with Flickr. ' 
                . $this->oauthClient->response['response']);
        }
        $resp = $this->oauthClient->extract_params($this->oauthClient->response['response']);
        return $resp;
    }
    
    private function saveToConfig(OutputInterface $output, $token, $tokenSecret)
    {
        $conf = array();
        $conf['oauth']['key'] = self::CONSUMER_KEY;
        $conf['oauth']['secret'] = self::CONSUMER_SECRET;
        $conf['oauth']['token'] = $token;
        $conf['oauth']['tokenSecret'] = $tokenSecret;
        
        $confFilename = $_SERVER['HOME'] . '/.flickrDownloadr';
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
