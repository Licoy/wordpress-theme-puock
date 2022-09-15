<?php

namespace Yurun\OAuthLogin\Baidu;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * api域名.
     */
    const API_DOMAIN = 'https://openapi.baidu.com/';

    /**
     * 非必须参数，登录和授权页面的展现样式，默认为“page”，具体参数定义请参考http://developer.baidu.com/wiki/index.php?title=docs/oauth/set.
     *
     * @var string
     */
    public $display;

    /**
     * 非必须参数，如传递“force_login=1”，则加载登录页时强制用户输入用户名和口令，不会从cookie中读取百度用户的登陆状态。
     *
     * @var string
     */
    public $forceLogin;

    /**
     * 非必须参数，如传递“confirm_login=1”且百度用户已处于登陆状态，会提示是否使用已当前登陆用户对应用授权。
     *
     * @var string
     */
    public $confirmLogin;

    /**
     * 非必须参数，如传递“login_type=sms”，授权页面会默认使用短信动态密码注册登陆方式。
     *
     * @var string
     */
    public $loginType;

    /**
     * 非必须参数。以空格分隔的权限列表，若不传递此参数，代表请求的数据访问操作权限与上次获取Access Token时一致。通过Refresh Token刷新Access Token时所要求的scope权限范围必须小于等于上次获取Access Token时授予的权限范围。关于权限的具体信息请参考http://developer.baidu.com/wiki/index.php?title=docs/oauth/baiduoauth/list.
     *
     * @var string
     */
    public $scope;

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
     * @param string $state       非必须参数，用于保持请求和回调的状态，授权服务器在回调时（重定向用户浏览器到“redirect_uri”时），会在Query Parameter中原样回传该参数。OAuth2.0标准协议建议，利用state参数来防止CSRF攻击。
     * @param array  $scope       非必须参数，以空格分隔的权限列表，若不传递此参数，代表请求用户的默认权限。关于权限的具体信息请参考“权限列表”。
     *
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'client_id'			    => $this->appid,
            'response_type'		 => 'code',
            'redirect_uri'		  => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'scope'				       => $scope,
            'state'				       => $this->getState($state),
            'display'			      => $this->display,
            'force_login'		   => $this->forceLogin,
            'confirm_login'		 => $this->confirmLogin,
            'login_type'		    => $this->loginType,
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getUrl('oauth/2.0/authorize', $option);
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
        $response = $this->http->get($this->getUrl('oauth/2.0/token'), [
            'grant_type'	    => 'authorization_code',
            'code'			        => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'client_id'		    => $this->appid,
            'client_secret'	 => $this->appSecret,
            'redirect_uri'	  => $this->getRedirectUri(),
        ]);
        $this->result = $response->json(true);
        if (!isset($this->result['error_description']))
        {
            return $this->accessToken = $this->result['access_token'];
        }
        else
        {
            throw new ApiException(isset($this->result['error_description']) ? $this->result['error_description'] : '', $response->httpCode());
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
        $response = $this->http->get($this->getUrl('rest/2.0/passport/users/getLoggedInUser', [
            'access_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
        ]));
        $this->result = $response->json(true);
        if (!isset($this->result['error_description']))
        {
            $this->openid = $this->result['uid'];

            return $this->result;
        }
        else
        {
            throw new ApiException(isset($this->result['error_description']) ? $this->result['error_description'] : '', $response->httpCode());
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
        $response = $this->http->get($this->getUrl('oauth/2.0/token'), [
            'grant_type'	    => 'refresh_token',
            'refresh_token'	 => $refreshToken,
            'client_id'		    => $this->appid,
            'client_secret'	 => $this->appSecret,
            'scope'			       => $this->scope,
        ]);
        $this->result = $response->json(true);
        if (!isset($this->result['error_description']))
        {
            return $this->accessToken = $this->result['access_token'];
        }
        else
        {
            throw new ApiException(isset($this->result['error_description']) ? $this->result['error_description'] : '', $response->httpCode());
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
}
