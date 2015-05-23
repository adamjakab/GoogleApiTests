<?php
namespace Jack\Google;
use Jack\FileSystem\FileReader;
use Jack\FileSystem\FileWriter;

class GoogleAuth {
    /** @var  array */
    private $config;

    /** @var  \Google_Client */
    private $client;

    /** @var  string */
    private $access_token;
    /**
     * @param array $config
     */
    public function __construct($config) {
        $this->config = $config;
        $this->client = new \Google_Client();
        $this->client->setClientId($config['client_id']);
        $this->client->setClientSecret($config['client_secret']);
        $this->client->setRedirectUri($config['redirect_uri']);
        $this->client->setScopes($config['auth_scopes']);
    }


    /**
     * @return bool|string
     * @throws \Exception
     */
    public function getValidAccessToken() {
        $validAccessToken = false;
        /** @var \Google_Auth_OAuth2 $auth */
        $auth = $this->client->getAuth();

        $accessToken = $this->getAccessToken();
        if($accessToken) {
            echo "\nchecking access token: " . $accessToken;
            $auth->setAccessToken($accessToken);
            $loginTicket = $auth->verifyIdToken();
            if($loginTicket instanceof \Google_Auth_LoginTicket) {
                $validAccessToken = $auth->getAccessToken();
            }
        }

        if(!$validAccessToken) {
            $refreshToken = $this->getRefreshToken();
            if($refreshToken) {
                echo "\nchecking refresh token: " . $refreshToken;
                $auth->refreshToken($refreshToken);
                $loginTicket = $auth->verifyIdToken();
                if($loginTicket instanceof \Google_Auth_LoginTicket) {
                    $validAccessToken = $auth->getAccessToken();
                }
            }
        }

        if(!$validAccessToken) {
            throw new \Exception("No valid Access Token");
        }

        echo "\nAccessToken: " . $validAccessToken;
        $this->storeAccessToken($validAccessToken);

        return $validAccessToken;
    }

    /**
     * @param string $accessToken
     */
    private function storeAccessToken($accessToken) {
        $tokenFilePath = ROOT_PATH.'/private/access_token';
        $fw = new FileWriter($tokenFilePath);
        $fw->open('w');
        $fw->writeLn($accessToken);
        $fw->close();
    }

    /**
     * @return bool|string
     */
    private function getAccessToken() {
        $accessToken = false;
        $tokenFilePath = ROOT_PATH.'/private/access_token';
        $fr = new FileReader($tokenFilePath);
        if($fr->open()) {
            $accessToken = $fr->readLine();
            $fr->close();
        }
        return $accessToken;
    }

    /**
     * @return bool|string
     */
    private function getRefreshToken() {
        $refreshToken = false;
        $tokenFilePath = ROOT_PATH.'/private/refresh_token';
        $fr = new FileReader($tokenFilePath);
        if($fr->open()) {
            $refreshToken = $fr->readLine();
            $fr->close();
        }
        return $refreshToken;
    }
}