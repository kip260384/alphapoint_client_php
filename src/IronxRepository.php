<?php

namespace Ironx;

require_once 'Frame.php';

use WebSocket\Client;

/**
 * Class IronxRepository
 * @package Ironx
 *
 * @mixin \Exception
 */
class IronxRepository
{
    /** @var \WebSocket\Client */
    private $client;

    /** @var Frame */
    private $query;

    public function __construct($client)
    {
        $this->client = $client;
        $this->query = new Frame();
    }

    /**
     * @param $token
     * @return $this
     *
     * response { "Authenticated": true, "SessionToken":"5eebbfc2-779f-4ca8-8d31-d348a6a1e4e2", "UserId": 10  }
     */
    public function webAuthenticateUser($user, $pass, $token = null)
    {
        $this->query->setN(ucfirst(__FUNCTION__));
        if ($token) {
            $this->query->setO(['SessionToken' => $token]);
        } else {
            $this->query->setO([
                'UserName'  => $user,
                'Password'  => $pass,
            ]);
        }
    }

    /**
     * @param $code
     * @return $this
     *
     * response { "Authenticated": false }
     */
    public function authenticate2FA($code)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO(['Code' => $code])
        ;
    }

    /**
     * @param $userId
     * @return $this
     *
     * response {"UserId":10,"UserName":"paveladmin","Email":"PKipriianov@IronFX.com","PasswordHash":"","PendingEmailCode":"","EmailVerified":true,"AccountId":12,"DateTimeCreated":"2019-01-29T11:50:10Z","AffiliateId":110,"RefererId":0,"OMSId":1,"Use2FA":false,"Salt":"","PendingCodeTime":"0001-01-01T00:00:00Z"}
     */
    public function getUserInfo($userId)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO(['UserId' => $userId])
        ;
    }

    /**
     * @param $name
     * @param $email
     * @param $pass
     * @return $this
     *
     * response { "UserId":15 }
     */
    public function registerNewUser($name, $email, $pass)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserInfo'  => [
                    'UserName'      => $name,
                    'Email'         => $email,
                    'passwordHash'  => $pass,
                ],
                'UserConfig' => [],
                'OperatorId' => 1,
            ])
        ;
    }

    /**
     * @param $userId
     * @param int $omsId
     * @return $this
     *
     * response '[19]'
     */
    public function getUserAccounts($userId, $omsId = 1)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserId'    => $userId,
                'OMSId'     => $omsId,
            ])
        ;
    }

    public function addAccount($name, $type, $riskType, $vLevel, $omsId = 1)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'LoyaltyProductId'      => null,
                'LoyaltyProductName'    => "",
                'AccountName'           => $name,
                'AccountType'           => $type,
                'RiskType'              => $riskType,
                'VerificationLevel'     => $vLevel,
                'OMSId'                 => $omsId,
            ])
        ;
    }


    /**
     * @param $userId
     * @param $accountId
     * @return $this
     *
     * response {"result":true,"errormsg":null,"errorcode":0,"detail":null}
     */
    public function addUserAccount($userId, $accountId)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserId'    => $userId,
                'AccountId' => $accountId,
            ])
        ;
    }

    /**
     * @param $userId
     * @param $name
     * @param $email
     * @param $emailVerified
     * @param $accountId
     * @param $use2fa
     * @return $this
     *
     * response {"UserId":15,"UserName":"php_test_220219","Email":"php_test_220219@mail.ru","PasswordHash":"","PendingEmailCode":"","EmailVerified":true,"AccountId":19,"DateTimeCreated":"2019-02-22T09:40:25Z","AffiliateId":112,"RefererId":0,"OMSId":1,"Use2FA":true,"Salt":"","PendingCodeTime":"0001-01-01T00:00:00Z"}
     */
    public function setUserInfo($userId, $name, $email, $emailVerified, $accountId, $use2fa)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserId'        => $userId,
                'UserName'      => $name,
                'Email'         => $email,
                'EmailVerified' => $emailVerified,
                'AccountId'     => $accountId,
                'Use2FA'        => $use2fa,
            ])
        ;
    }

    public function setUserConfig($userId, array $config)
    {
        $formattedConfig = [];
        foreach ($config as $key => $value) {
            $formattedConfig[] = ['Key' => (string)$key, 'Value' => $value];
        }

        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserId' => $userId,
                'Config' => $formattedConfig,
            ])
        ;
    }

    /**
     * @param $userId
     * @param $permission
     * @return $this
     *
     * response {"result":true,"errormsg":null,"errorcode":0,"detail":null}
     */
    public function addUserPermission($userId, $permission)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserId'        => $userId,
                'Permission'    => $permission,
                'Value'         => $permission
            ])
        ;
    }

    /**
     * @param $userId
     * @param $permission
     * @return $this
     *
     * response {"result":true,"errormsg":null,"errorcode":0,"detail":null}
     */
    public function revokeUserPermission($userId, $permission)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'UserId'        => $userId,
                'Permission'    => $permission,
            ])
        ;
    }

    /**
     * @param $accountId
     * @param int $omsId
     * @return $this
     *
     * response {"OMSID":1,"AccountId":20,"AccountName":"php_test_acc","AccountHandle":"","FirmId":null,"FirmName":null,"AccountType":"Liability","FeeGroupID":0,"ParentID":0,"RiskType":"Normal","VerificationLevel":0,"FeeProductType":"BaseProduct","FeeProduct":0,"RefererId":0,"LoyaltyProductId":0,"LoyaltyEnabled":false,"SupportedVenueIds":[]}
     */
    public function getAccountInfo($accountId, $omsId = 1)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'OMSId'        => $omsId,
                'AccountId'    => $accountId,
            ])
        ;
    }

    public function updateAccount(array $params)
    {
        $paramsList = [
            'OMSID', 'AccountId', 'AccountName', 'AccountHandle', 'FirmId', 'FirmName', 'AccountType', 'FeeGroupID',
            'ParentID', 'RiskType', 'VerificationLevel', 'FeeProductType', 'FeeProduct', 'RefererId', 'LoyaltyProductId',
            'LoyaltyEnabled', 'SupportedVenueIds',
        ];

        foreach ($paramsList as $name) {
            if (!key_exists($name, $params)) {
                throw new \Exception("Param $name is missed");
            }
        }

        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO($params)
        ;
    }

    public function createDepositTicket($accountId, $assetId, $amount, $assetName, $operatorId, $requestUser,
                                        $fullname, $depositInfo = '', $comment = '', $status = 'New', $omsId = 1)
    {
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'OMSId'         => $omsId,
                'AccountId'     => $accountId,
                'AssetId'       => $assetId,
                'Amount'        => $amount,
                'AssetName'     => $assetName,
                'OperatorId'    => $operatorId,
                'RequestUser'   => $requestUser,
                'DepositInfo'   => $depositInfo,
                'Fullname'      => $fullname,
                'Comments'      => $comment,
                'Status'        => $status,
            ])
        ;
    }

    /**
     * @param $accountId
     * @param $productId
     * @param $amount
     * @param $fullname
     * @param string $comment
     * @param int $omsId
     * @return $this
     *
     * response {"result":false,"errormsg":"Invalid Request","errorcode":100,"detail":"Insufficient Balance"}
     */
    public function createWithdrawTicket($accountId, $productId, $amount, $fullname, $comment = '', $omsId = 1)
    {
        $templateForm = json_encode([
            'Fullname'      => $fullname,
            'Comments'      => $comment,
        ]);

        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'OMSId'         => $omsId,
                'AccountId'     => $accountId,
                'ProductId'     => $productId,
                'Amount'        => $amount,
                'TemplateForm'  => $templateForm,
            ])
        ;
    }

    /**
     * @param $accountId
     * @param $productId
     * @param $crAmnt
     * @param $drAmnt
     * @param string $comment
     * @param int $omsId
     * @return $this
     *
     * response {"result":true,"errormsg":null,"errorcode":0,"detail":null}
     */
    public function submitAccountLedgerEntry($accountId, $productId, $crAmnt, $drAmnt, $comment = '', $omsId = 1)
    {
        if ($crAmnt > 0 && $drAmnt > 0) {
            throw new \Exception('Only one of credit or deposit amount can be positive.');
        }
        $this->query
            ->setN(ucfirst(__FUNCTION__))
            ->setO([
                'OMSId'         => $omsId,
                'AccountId'     => $accountId,
                'ProductId'     => $productId,
                'CR_Amt'        => $crAmnt,
                'DR_Amt'        => $drAmnt,
                'Comment'       => $comment,
            ])
        ;
    }

    public function setCounters($m, $i)
    {
        $this->query
            ->setM($m)
            ->setI($i);
    }

    public function send()
    {
//        var_dump($this->query->asJson());die;
        $this->client->send($this->query->asJson());

        $resp = $this->client->receive();

        $respFrame = new Frame($resp);

//        var_dump($resp);
        return $respFrame->getO();
    }

}
