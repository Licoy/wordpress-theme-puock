<?php

namespace Yurun\OAuthLogin\GitLab;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * 授权接口域名.
     */
    public $authDomain = 'https://gitlab.com/';

    /**
     * api域名.
     */
    public $apiDomain = 'https://gitlab.com/api/v4/';

    /**
     * 设置授权接口域名.
     *
     * @param string $domain
     * @return $this
     */
    public function setAuthDomain(string $domain)
    {
        $this->authDomain = $domain;

        return $this;
    }

    /**
     * 设置api域名.
     *
     * @param string $domain
     * @return $this
     */
    public function setApiDomain(string $domain)
    {
        $this->apiDomain = $domain;

        return $this;
    }

    /**
     * 获取登录授权url地址
     *
     * @param string $name 跟在域名后的文本
     * @param array $params GET参数
     *
     * @return string
     */
    public function getAuthLoginUrl($name, $params = [])
    {
        return $this->authDomain . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 获取url地址
     *
     * @param string $name 跟在域名后的文本
     * @param array $params GET参数
     *
     * @return string
     */
    public function getUrl($name, $params = [])
    {
        return $this->apiDomain . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 第一步:获取登录页面跳转url.
     *
     * @param string $callbackUrl 登录回调地址
     * @param string $state 状态值，不传则自动生成，随后可以通过->state获取。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。一般为每个用户登录时随机生成state存在session中，登录回调中判断state是否和session中相同
     * @param array $scope 请求用户授权时向用户显示的可进行授权的列表。可空
     *
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'client_id' => $this->appid,
            'redirect_uri' => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'response_type' => 'code',
            'state' => $this->getState($state),
            'scope' => null === $scope ? $this->scope : $scope,
        ];
        if (null === $this->loginAgentUrl) {
            return $this->getAuthLoginUrl('oauth/authorize', $option);
        } else {
            return $this->loginAgentUrl . '?' . $this->http_build_query($option);
        }
    }

    /**
     * 第二步:处理回调并获取access_token。与getAccessToken不同的是会验证state值是否匹配，防止csrf攻击。
     *
     * @param string $storeState 存储的正确的state
     * @param string $code 第一步里$redirectUri地址中传过来的code，为null则通过get参数获取
     * @param string $state 回调接收到的state，为null则通过get参数获取
     *
     * @return string
     */
    protected function __getAccessToken($storeState, $code = null, $state = null)
    {
        $response = $this->http->post($this->getAuthLoginUrl('oauth/token'), [
            'client_id' => $this->appid,
            'client_secret' => $this->appSecret,
            'code' => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'redirect_uri' => $this->getRedirectUri(),
            'state' => isset($state) ? $state : (isset($_GET['state']) ? $_GET['state'] : ''),
            'grant_type' => 'authorization_code',
        ]);
        $this->result = $response->json(true);
        if (isset($this->result['error_description'])) {
            throw new ApiException($this->result['error_description'], 0);
        } else {
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
        $this->result = $this->http->header('Authorization', "Bearer {$token}")
            ->get($this->getUrl('user'))
            ->json(true);
        if (isset($this->result['error_description'])) {
            throw new ApiException($this->result['error_description'], 0);
        } else {
            $this->openid = $this->result['id'];

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
        $response = $this->http->post($this->getAuthLoginUrl('oauth/token'), [
            'client_id' => $this->appid,
            'client_secret' => $this->appSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'redirect_uri' => $this->getRedirectUri(),
        ]);
        $this->result = $response->json(true);
        if (isset($this->result['error_description'])) {
            throw new ApiException($this->result['error_description'], 0);
        } else {
            return $this->accessToken = $this->result['access_token'];
        }
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
        try {
            $this->getUserInfo($accessToken);

            return true;
        } catch (ApiException $e) {
            return false;
        }
    }
}
