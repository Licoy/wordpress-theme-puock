<?php

namespace Yurun\OAuthLogin\FeiShu;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * api接口域名.
     */
    const API_DOMAIN = 'https://open.feishu.cn/';

    public $appAccessToken;

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
        return static::API_DOMAIN . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
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
     *
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'app_id'       => $this->appid,
            'redirect_uri' => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'scope'        => null === $scope ? $this->scope : $scope,
            'state'        => $this->getState($state),
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getAuthLoginUrl('open-apis/authen/v1/authorize', $option);
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
        $this->getAppAccessToken();

        $this->result = $this->http->rawHeaders(["Authorization: Bearer {$this->appAccessToken}", 'Content-Type: application/json; charset=utf-8'])
            ->post($this->getAuthLoginUrl('open-apis/authen/v1/oidc/access_token'), [
            'grant_type' => 'authorization_code',
            'code'       => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
        ], 'json')->json(true);

        if (!empty($this->result['code']))
        {
            throw new ApiException($this->result['message'], $this->result['code']);
        }
        else
        {
            return $this->accessToken = $this->result['data']['access_token'];
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
            ->get($this->getUrl('open-apis/authen/v1/user_info'))
            ->json(true);
        if (!empty($this->result['code']))
        {
            throw new ApiException($this->result['msg'], $this->result['code']);
        }
        else
        {
            $this->openid = $this->result['data']['open_id'];

            return $this->result;
        }
    }

    /**
     * 刷新AccessToken续期
     *
     * @param string $refreshToken
     *
     * @return string
     */
    public function refreshToken($refreshToken)
    {
        $this->getAppAccessToken();

        $this->result = $this->http->rawHeaders(["Authorization: Bearer {$this->appAccessToken}", 'Content-Type: application/json; charset=utf-8'])
            ->post($this->getAuthLoginUrl('open-apis/authen/v1/refresh_access_token'), [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken ,
        ], 'json')->json(true);

        if (!empty($this->result['code']))
        {
            throw new ApiException($this->result['message'], $this->result['code']);
        }
        else
        {
            return $this->accessToken = $this->result['data']['access_token'];
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

    public function getAppAccessToken()
    {
        $this->result = $this->http->post($this->getUrl('open-apis/auth/v3/app_access_token/internal'), [
            'app_id'     => $this->appid,
            'app_secret' => $this->appSecret
        ])->json(true);

        if (!empty($this->result['code']))
        {
            throw new ApiException($this->result['msg'], $this->result['code']);
        }
        else
        {
            $this->appAccessToken = $this->result['app_access_token'];

            return $this->result;
        }
    }
}
