<?php
namespace Jack\Google;

use Jack\FileSystem\FileReader;
use Jack\FileSystem\FileWriter;

class GoogleAuth
{
    /** @var  array */
    private $config;

    /** @var  \Google_Client */
    private $client;

    /** @var  string */
    private $access_token;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->client = new \Google_Client();
        $this->client->setClientId($config['client_id']);
        $this->client->setClientSecret($config['client_secret']);
        $this->client->setRedirectUri($config['redirect_uri']);
        $this->client->setScopes($config['auth_scopes']);
        $this->setValidAccessToken();
    }

    /**
     * @return bool|string
     * @throws \Exception
     */
    private function setValidAccessToken()
    {
        $validAccessToken = false;
        /** @var \Google_Auth_OAuth2 $auth */
        $auth = $this->client->getAuth();

        $accessToken = $this->getStoredAccessToken();
        if ($accessToken) {
            try {
                $auth->setAccessToken($accessToken);
                $loginTicket = $auth->verifyIdToken();
                if ($loginTicket instanceof \Google_Auth_LoginTicket) {
                    $validAccessToken = $auth->getAccessToken();
                }
            } catch(\Google_Auth_Exception $e) {
                echo "\nBad Access Token: " . $e->getMessage();
            }
        }

        if (!$validAccessToken) {
            $refreshToken = $this->getStoredRefreshToken();
            if ($refreshToken) {
                $auth->refreshToken($refreshToken);
                $loginTicket = $auth->verifyIdToken();
                if ($loginTicket instanceof \Google_Auth_LoginTicket) {
                    $validAccessToken = $auth->getAccessToken();
                }
            }
        }

        if (!$validAccessToken) {
            throw new \Exception("No valid Access Token");
        }

        $this->storeAccessToken($validAccessToken);
        echo "Access token expires in: " . $this->getAccessTokenRemainingTime($validAccessToken);
        $this->access_token = $validAccessToken;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @return bool|string
     */
    private function getStoredRefreshToken()
    {
        $refreshToken = false;
        $tokenFilePath = ROOT_PATH . '/private/refresh_token';
        $fr = new FileReader($tokenFilePath);
        if ($fr->open()) {
            $refreshToken = $fr->readLine();
            $fr->close();
        }
        return $refreshToken;
    }

    /**
     * @param string $accessToken
     */
    private function storeAccessToken($accessToken)
    {
        $tokenFilePath = ROOT_PATH . '/private/access_token';
        $fw = new FileWriter($tokenFilePath);
        $fw->open('w');
        $fw->writeLn($accessToken);
        $fw->close();
    }

    /**
     * @param string $accessToken
     * @return integer mixed
     */
    public function getAccessTokenRemainingTime($accessToken)
    {
        $at = json_decode($accessToken);
        $now = time();
        return ($at->created + $at->expires_in - $now);
    }

    /**
     * @return bool|string
     */
    private function getStoredAccessToken()
    {
        $accessToken = false;
        $tokenFilePath = ROOT_PATH . '/private/access_token';
        $fr = new FileReader($tokenFilePath);
        if ($fr->open()) {
            $accessToken = $fr->readLine();
            $fr->close();
        }
        return $accessToken;
    }
}