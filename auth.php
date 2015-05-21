<?php
/**
 * Created by PhpStorm.
 * User: jackisback
 * Date: 21/05/15
 * Time: 23.23
 */
error_reporting(E_ALL);
ini_set("display_errors", true);

session_start();
@include_once __DIR__ . '/vendor/autoload.php';
@include_once __DIR__ . "/vendor/google/apiclient/examples/templates/base.php";

//@todo: load these
$client_id = '902693345462-3oegm5aa0kp3tprnubtcpneogg23bfhn.apps.googleusercontent.com';
$client_secret = 'YAGOOJJCVtPoLUvgm-IMrOIS';
$redirect_uri = 'http://localhost:63342/GoogleApiTests/auth.php';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes('email');

/************************************************
If we're logging out we just need to clear our
local access token in this case
 ************************************************/
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
}

/************************************************
If we have a code back from the OAuth 2.0 flow,
we need to exchange that with the authenticate()
function. We store the resultant access token
bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

/************************************************
If we have an access token, we can make
requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    $authUrl = $client->createAuthUrl();
}

/************************************************
If we're signed in we can go ahead and retrieve
the ID token, which is part of the bundle of
data that is exchange in the authenticate step
- we only need to do a network call if we have
to retrieve the Google certificate to verify it,
and that can be cached.
 ************************************************/
if ($client->getAccessToken()) {
    $_SESSION['access_token'] = $client->getAccessToken();
    $token_data = $client->verifyIdToken()->getAttributes();
}

echo ("User Query - Retrieving An Id Token");
if (strpos($client_id, "googleusercontent") == false) {
    echo missingClientSecretsWarning();
    exit;
}
?>
<div class="box">
    <div class="request">
        <?php
        if (isset($authUrl)) {
            echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
        } else {
            echo "<a class='logout' href='?logout'>Logout</a>";
        }
        ?>
    </div>

    <div class="data">
        <?php
        if (isset($token_data)) {
            var_dump($token_data);
        }
        ?>
    </div>
</div>

