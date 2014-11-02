<?php
/**
 * @see https://github.com/themattharris/tmhOAuthExamples/blob/master/cli/oob.php
 */

require_once './vendor/autoload.php';

$confFilename = '.flickrDownloadr';
$conf = [
    'oauth' => [
        'key' => '3365341effaf533f4fe95f6629a2c9a8',
        'secret' => '9c21dac1df1c16a3',
    ]
];

$oauth = new tmhOAuth([
    'consumer_key' => $conf['oauth']['key'],
    'consumer_secret' => $conf['oauth']['secret'],
]);

$requestCode = $oauth->apponly_request([
    'without_bearer' => true,
    'method' => 'POST',
    'url' => 'https://www.flickr.com/services/oauth/request_token',
    'params' => [
        'oauth_callback' => 'oob'
    ]
]);

$requestCreds = $oauth->extract_params($oauth->response['response']);
$oauth->config['user_token'] = $requestCreds['oauth_token'];
$oauth->config['user_secret'] = $requestCreds['oauth_token_secret'];

echo 'Copy and paste this URL into your web browser and follow the prompts to get a pin code.' . "\n";
$authUrl = 'https://www.flickr.com/services/oauth/authorize?oauth_token=' . $requestCreds['oauth_token'] . '&perms=read';
echo $authUrl . "\n";

echo 'What was the Pin Code?: ';
$handle = fopen("php://stdin","r");
$data = fgets($handle);
$pinCode = trim($data);

$accessCode = $oauth->user_request([
    'method' => 'POST',
    'url' => 'https://www.flickr.com/services/oauth/access_token',
    'params' => [
        'oauth_verifier' => $pinCode,
        'oauth_token' => $requestCreds['oauth_token'],
    ],
]);

$accessCreds = $oauth->extract_params($oauth->response['response']);

$conf['oauth']['token'] = $accessCreds['oauth_token'];
$conf['oauth']['tokenSecret'] = $accessCreds['oauth_token_secret'];

$neonEncoder = new \Nette\Neon\Encoder();
$confEncoded = $neonEncoder->encode($conf, 1);
if (file_put_contents($confFilename, $confEncoded) !== FALSE) {
    echo 'Authorization OK, credentials saved into ' . $confFilename . PHP_EOL;
} else {
    echo 'Error saving into file';
}
