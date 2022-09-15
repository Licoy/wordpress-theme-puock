<?php

namespace Yurun\OAuthLogin\Weibo;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * api域名.
     */
    const API_DOMAIN = 'https://api.weibo.com/';

    /**
     * 当display=mobile时，使用该域名.
     */
    const API_MOBILE_DOMAIN = 'https://open.weibo.cn/';

    /**
     * 授权页面的终端类型，取值见微博文档。http://open.weibo.com/wiki/Oauth2/authorize.
     *
     * @var string
     */
    public $display;

    /**
     * 是否强制用户重新登录，true：是，false：否。默认false。
     *
     * @var bool
     */
    public $forcelogin = false;

    /**
     * 授权页语言，缺省为中文简体版，en为英文版。
     *
     * @var string
     */
    public $language;

    /**
     * 获取用户资料时传的参数，可空.
     *
     * @var string
     */
    public $screenName;

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
     * 获取display=mobile时的url地址
     *
     * @param string $name   跟在域名后的文本
     * @param array  $params GET参数
     *
     * @return string
     */
    public function getMobileUrl($name, $params)
    {
        return static::API_MOBILE_DOMAIN . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
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
            'client_id'			   => $this->appid,
            'redirect_uri'		 => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'scope'				      => $scope,
            'state'				      => $this->getState($state),
            'display'			     => $this->display,
            'forcelogin'		   => $this->forcelogin,
            'language'			    => $this->language,
        ];
        if (null === $this->loginAgentUrl)
        {
            if ('mobile' === $this->display)
            {
                return $this->getMobileUrl('oauth2/authorize', $option);
            }
            else
            {
                return $this->getUrl('oauth2/authorize', $option);
            }
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
        $this->result = $this->http->post($this->getUrl('oauth2/access_token'), [
            'client_id'		    => $this->appid,
            'client_secret'	 => $this->appSecret,
            'grant_type'	    => 'authorization_code',
            'code'			        => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'redirect_uri'	  => $this->getRedirectUri(),
        ])->json(true);
        if (isset($this->result['error_code']))
        {
            throw new ApiException($this->result['error'], $this->result['error_code']);
        }
        else
        {
            $this->openid = $this->result['uid'];

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
        $this->result = $this->http->get($this->getUrl('2/users/show.json', [
            'access_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
            'uid'			        => $this->openid,
            'screenName'	   => $this->screenName,
        ]))->json(true);
        if (isset($this->result['error_code']))
        {
            throw new ApiException($this->result['error'], $this->result['error_code']);
        }
        else
        {
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
        // 微博不支持刷新
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
        $this->result = $this->http->post($this->getUrl('oauth2/get_token_info'), [
            'access_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
        ])->json(true);
        if (isset($this->result['error_code']))
        {
            throw new ApiException($this->result['error'], $this->result['error_code']);
        }
        else
        {
            return $this->result['expire_in'] > 0;
        }
    }
}
