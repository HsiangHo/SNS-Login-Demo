<?php

require_once 'twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');
$config = [
    // key and secret of your application
    'consumer_key'      => 'your consumer_key',
    'consumer_secret'   => 'your consumer_secret',

    // callbacks for your application
    'url_callback'      => 'your callback url'
];



if (!isset($_SESSION['oauth_token']) || empty($oauth_verifier)){
    $twitteroauth = new TwitterOAuth($config['consumer_key'], $config['consumer_secret']);
    $request_token = $twitteroauth->oauth(
        'oauth/request_token', [
            'oauth_callback' => $config['url_callback']
        ]
    );

    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    $url = $twitteroauth->url(
        'oauth/authorize', [
            'oauth_token' => $request_token['oauth_token']
        ]
    );

    header('Location: '. $url);
} else {
    $oauth_token = $_SESSION['oauth_token'];
    unset($_SESSION['oauth_token']);
    $twitteroauth = new TwitterOAuth($config['consumer_key'], $config['consumer_secret']);

    $access_token = $twitteroauth->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier,"oauth_token"=>$oauth_token]);

    $twitteroauth = new TwitterOAuth($config['consumer_key'], $config['consumer_secret'],
        $access_token['oauth_token'],$access_token['oauth_token_secret']);

    $content = $twitteroauth->get("account/verify_credentials");
    print_r($content);

    $status = $twitteroauth->post(
        "statuses/update", [
            "status" => "This is my first Tweet 😎。 ----- From My GitHub https://github.com/hsiangho"
        ]
    );
    print_r($status);
}
