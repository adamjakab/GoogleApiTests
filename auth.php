<?php

use Jack\FileSystem\FileReader;
use Jack\FileSystem\FileWriter;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Created by PhpStorm.
 * User: jackisback
 * Date: 21/05/15
 * Time: 23.23
 */
error_reporting(E_ALL);
ini_set("display_errors", true);
defined('ROOT_PATH') || define('ROOT_PATH', __DIR__);

session_start();
@include_once __DIR__ . '/vendor/autoload.php';

//init some variables
$tokenFilePath = ROOT_PATH.'/private/refresh_token';
$refreshToken = null;
$accessToken = null;
$token_data = null;
$fs = new FileSystem();

//get configuration
$config_file = 'private/gapp.yml';
$configParser = new \Jack\Configuration\Parser();
try {
    $config = $configParser->getConfiguration($config_file);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

$client = new Google_Client();
$client->setClientId($config['client_id']);
$client->setClientSecret($config['client_secret']);
$client->setRedirectUri($config['redirect_uri']);
$client->setScopes($config['auth_scopes']);

/************************************************
If we're logging out we just need to clear our
local access token in this case
 ************************************************/
if (isset($_REQUEST['logout'])) {
    $fs->remove($tokenFilePath);
}

//get saved refresh token
$fr = new FileReader($tokenFilePath);
if($fr->open()) {
    $refreshToken = $fr->readLine();
    $fr->close();
}


/************************************************
If we have a code back from the OAuth 2.0 flow,
we need to exchange that with the authenticate()
function. We store the resultant access token
bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $accessToken = json_decode($client->getAccessToken());
    $fw = new FileWriter($tokenFilePath);
    $fw->open('w');
    $fw->writeLn($accessToken->refresh_token);
    $fw->close();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

/************************************************
If we have an access token, we can make
requests, else we generate an authentication URL.
 ************************************************/
if ($refreshToken) {
    $client->refreshToken($refreshToken);
    //$client->setAccessToken($accessToken);
    $token_data = $client->verifyIdToken()->getAttributes();
} else {
    if($config['offline_access'] == true) {
        $client->setAccessType("offline");
    }
    $authUrl = $client->createAuthUrl();
}

?>

<?php if(!$token_data) {
    if($config['offline_access'] == true) {
        $client->setAccessType("offline");
    }
    $authUrl = $client->createAuthUrl();
    ?>
    <pre><?php echo $authUrl; ?></pre>
    <a class='login' href='<?php echo $authUrl; ?>'>Connect Me!</a>
<?php } else {
    var_dump($token_data);
    ?>
    <a class='logout' href='?logout'>Logout</a>
<?php } ?>
