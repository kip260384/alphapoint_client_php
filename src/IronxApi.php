<?php

namespace Kip\Ironx;

use PragmaRX\Google2FA\Google2FA;
use WebSocket\Client;

require_once 'IronxRepository.php';
require_once 'RepositoryHandler.php';

class IronxApi
{
    private $repo;
    private $m;
    private $i;
    private $userName;
    private $pass;
    private $token;
    private $secret;
    private $ga;

    public function __construct($user, $pass, $secret, $url)
    {
        $client = new Client($url);
        $repo = new IronxRepository($client);
        $this->repo = new RepositoryHandler($repo);
        $this->userName = $user;
        $this->pass = $pass;
        $this->secret = $secret;
        $this->ga = new Google2FA();
        $this->ga->setEnforceGoogleAuthenticatorCompatibility(false);
    }

    public function init()
    {
        $this->m = 0;
        $this->i = 0;
        $this->auth();
        var_dump($this->repo->getUserInfo(15));

        //TODO check token in cache
    }

    protected function auth()
    {
        if ($this->token) {
            $resp = $this->repo->webAuthenticateUser(null, null, $this->token);
            if ($resp['Authenticated']) {
                return true;
            }
        } else {
            $resp = $this->repo->webAuthenticateUser($this->userName, $this->pass);
        }

        if ($resp['Authenticated'] && $resp['Requires2FA']) {
            $otp = $this->ga->getCurrentOtp($this->secret);
            $resp = $this->repo->authenticate2FA($otp);
        }

        if ($resp['Authenticated']) {
            //TODO Cache
            $this->token = $resp['SessionToken'] ?? null;
        } else {
            throw new \Exception('Authentication failed. '.$resp['Errormsg'] ?? '');
        }

        return true;
    }


    public function changeAccountVerificationLevel($accountId, $newLevel)
    {
        $params = $this->repo->getAccountInfo($accountId);
        $params['VerificationLevel'] = $newLevel;

        $changeResp = $this->repo->updateAccount($params);

        return $changeResp;
    }

}
