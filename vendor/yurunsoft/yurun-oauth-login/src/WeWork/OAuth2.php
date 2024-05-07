<?php

namespace Yurun\OAuthLogin\WeWork;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * api接口域名.
     */
    const API_DOMAIN = 'https://qyapi.weixin.qq.com/';

    /**
     * 开放域名.
     */
    const OPEN_DOMAIN = 'https://open.weixin.qq.com/';

    /**
     * Web登录域名.
     */
    const LOGIN_DOMAIN = 'https://login.work.weixin.qq.com/';

    /**
     * agentid
     *
     * @var string
     */
    public $agentid;

    /**
     * 构造方法.
     *
     * @param string $appid       应用的唯一标识
     * @param string $appSecret   appid对应的密钥
     * @param string $callbackUrl 登录回调地址
     * @param string $agentid     应用agentid
     */
    public function __construct($appid = null, $appSecret = null, $callbackUrl = null, $agentid = null)
    {
        parent::__construct($appid, $appSecret, $callbackUrl);
        $this->agentid = $agentid;
    }

    /**
     * 获取登录授权url地址
     *
     * @param string $name   跟在域名后的文本
     * @param array  $params GET参数
     *
     * @return string
     */
    public function getAuthLoginUrl($name, $params = [])
    {
        return static::OPEN_DOMAIN . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 获取Web登录授权url地址
     *
     * @param string $name   跟在域名后的文本
     * @param array  $params GET参数
     *
     * @return string
     */
    public function getWebLoginUrl($name, $params = [])
    {
        return static::LOGIN_DOMAIN . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 获取url地址
     *
     * @param string $name   跟在域名后的文本
     * @param array  $params GET参数
     *
     * @return string
     */
    public function getUrl($name, $params = [])
    {
        return static::API_DOMAIN . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 第一步:获取登录页面跳转url.
     *
     * @param string $callbackUrl 登录回调地址
     * @param string $state       状态值，不传则自动生成，随后可以通过->state获取。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。一般为每个用户登录时随机生成state存在session中，登录回调中判断state是否和session中相同
     * @param array  $scope       请求用户授权时向用户显示的可进行授权的列表。可空
     * @link https://developer.work.weixin.qq.com/document/path/91022
     *
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'appid'         => $this->appid,
            'agentid'       => $this->agentid,
            'redirect_uri'  => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'scope'         => null === $scope ? $this->scope : $scope,
            'state'         => $this->getState($state),
            'response_type' => 'code',
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getAuthLoginUrl('connect/oauth2/authorize', $option);
        }
        else
        {
            return $this->loginAgentUrl . '?' . $this->http_build_query($option);
        }
    }

    /**
     * 第一步：生成企业微信扫码登录页面，获取登录页面跳转url.
     *
     * @param string $callbackUrl
     * @param string $state
     * @param string $loginType
     * @link https://developer.work.weixin.qq.com/document/path/98152
     *
     * @return string
     */
    public function getWebAuthUrl($callbackUrl = null, $state = null, $loginType = 'CorpApp')
    {
        $option = [
            'appid'        => $this->appid,
            'agentid'      => $this->agentid,
            'login_type'   => $loginType,
            'redirect_uri' => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'state'        => $this->getState($state)
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getWebLoginUrl('wwlogin/sso/login', $option);
        }
        else
        {
            return $this->loginAgentUrl . '?' . $this->http_build_query($option);
        }
    }

    /**
     * 第二步:处理回调并获取access_token。与getAccessToken不同的是会验证state值是否匹配，防止csrf攻击。
     *
     * @param string $storeState 存储的正确的state
     * @param string $code       第一步里$redirectUri地址中传过来的code，为null则通过get参数获取
     * @param string $state      回调接收到的state，为null则通过get参数获取
     *
     * @return string
     */
    protected function __getAccessToken($storeState, $code = null, $state = null)
    {
        $this->result = $this->http->get($this->getUrl('cgi-bin/gettoken'), [
            'corpid'     => $this->appid,
            'corpsecret' => $this->appSecret
        ])->json(true);

        if (!empty($this->result['errcode']))
        {
            throw new ApiException($this->result['errmsg'], $this->result['errcode']);
        }
        else
        {
            return $this->accessToken = $this->result['access_token'];
        }
    }

    /**
     * 获取用户资料.
     *
     * @param string $accessToken
     *
     * @return array
     */
    public function getUserInfo($accessToken = null)
    {
        $token = null === $accessToken ? $this->accessToken : $accessToken;
        $this->result = $this->http->get($this->getUrl('cgi-bin/auth/getuserinfo', [
                'access_token' => $token,
                'code'         => isset($_GET['code']) ? $_GET['code'] : '',
            ]))
            ->json(true);

        if (!empty($this->result['errcode']))
        {
            throw new ApiException($this->result['errmsg'], $this->result['errcode']);
        }
        else
        {
            $this->openid = isset($this->result['userid']) ? $this->result['userid'] : $this->result['openid'];

            return $this->result;
        }
    }

    /**
     * Get user detail info.
     *
     * @param string $userTicket
     * @param string|null $accessToken
     * @return array
     */
    public function getUserDetail($userTicket, $accessToken = null)
    {
        $token = null === $accessToken ? $this->accessToken : $accessToken;
        $this->result = $this->http->post($this->getUrl('cgi-bin/auth/getuserdetail', ['access_token' => $token]), [
                'user_ticket' => $userTicket,
            ], 'json')
            ->json(true);

        if (!empty($this->result['errcode']))
        {
            throw new ApiException($this->result['errmsg'], $this->result['errcode']);
        }
        else
        {
            $this->openid = $this->result['userid'];

            return $this->result;
        }
    }

    /**
     * 刷新AccessToken续期
     *
     * @param string $refreshToken
     *
     * @return bool
     */
    public function refreshToken($refreshToken)
    {
        return false;
    }

    /**
     * 检验授权凭证AccessToken是否有效.
     *
     * @param string $accessToken
     *
     * @return bool
     */
    public function validateAccessToken($accessToken = null)
    {
        try
        {
            $this->getUserInfo($accessToken);

            return true;
        }
        catch (ApiException $e)
        {
            return false;
        }
    }
}
