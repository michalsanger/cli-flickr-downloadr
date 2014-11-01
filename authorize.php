<?php
/**
 * @see https://github.com/themattharris/tmhOAuthExamples/blob/master/cli/oob.php
 */

require_once './vendor/autoload.php';

$oauth = new tmhOAuth([
    'consumer_key' => '3365341effaf533f4fe95f6629a2c9a8',
    'consumer_secret' => '9c21dac1df1c16a3',
]);

$code = $oauth->apponly_request([
    'without_bearer' => true,
    'method' => 'POST',
    'url' => 'https://www.flickr.com/services/oauth/request_token',
    'params' => [
        'oauth_callback' => 'oob'
    ]
]);

$creds = $oauth->extract_params($oauth->response['response']);
$oauth->config['user_token'] = $creds['oauth_token'];
$oauth->config['user_secret'] = $creds['oauth_token_secret'];

echo 'Copy and paste this URL into your web browser and follower the prompts to get a pin code.' . "\n";
$authUrl = 'https://www.flickr.com/services/oauth/authorize?oauth_token=' . $creds['oauth_token'] . '&perms=read';
echo $authUrl . "\n";

echo 'What was the Pin Code?: ';
$handle = fopen("php://stdin","r");
$data = fgets($handle);
$pinCode = trim($data);

$code = $oauth->user_request([
    'method' => 'POST',
    'url' => 'https://www.flickr.com/services/oauth/access_token',
    'params' => [
        'oauth_verifier' => $pinCode,
        'oauth_token' => $creds['oauth_token'],
    ],
]);

$creds = $oauth->extract_params($oauth->response['response']);
//var_dump($creds);

$metadata = new Rezzza\Flickr\Metadata('3365341effaf533f4fe95f6629a2c9a8', '9c21dac1df1c16a3');
$metadata->setOauthAccess($creds['oauth_token'], $creds['oauth_token_secret']);

$factory  = new Rezzza\Flickr\ApiFactory($metadata, new Rezzza\Flickr\Http\GuzzleAdapter());

$setsList = $factory->call('flickr.photosets.getList');

var_dump($setsList);