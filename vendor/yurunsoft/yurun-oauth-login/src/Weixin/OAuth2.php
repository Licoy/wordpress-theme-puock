<?php

namespace Yurun\OAuthLogin\Weixin;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * api接口域名.
     */
    const API_DOMAIN = 'https://api.weixin.qq.com/';

    /**
     * 开放平台域名.
     */
    const OPEN_DOMAIN = 'https://open.weixin.qq.com/';

    /**
     * 语言，默认为zh_CN.
     *
     * @var string
     */
    public $lang = 'zh_CN';

    /**
     * openid从哪个字段取，默认为openid.
     *
     * @var int
     */
    public $openidMode = OpenidMode::OPEN_ID;

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
        if ('http' === substr($name, 0, 4))
        {
            $domain = $name;
        }
        else
        {
            $domain = static::API_DOMAIN . $name;
        }

        return $domain . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 第一步:获取PC页登录所需的url，一般用于生成二维码
     *
     * @param string $callbackUrl 登录回调地址
     * @param string $state       状态值，不传则自动生成，随后可以通过->state获取。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。一般为每个用户登录时随机生成state存在session中，登录回调中判断state是否和session中相同
     * @param array  $scope       请求用户授权时向用户显示的可进行授权的列表。可空，默认snsapi_login
     *
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'appid'				       => $this->appid,
            'redirect_uri'		  => null === $callbackUrl ? (null === $this->callbackUrl ? (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') : $this->callbackUrl) : $callbackUrl,
            'response_type'		 => 'code',
            'scope'				       => null === $scope ? (null === $this->scope ? 'snsapi_login' : $this->scope) : $scope,
            'state'				       => $this->getState($state),
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getUrl(static::OPEN_DOMAIN . 'connect/qrconnect', $option) . '#wechat_redirect';
        }
        else
        {
            return $this->loginAgentUrl . '?' . $this->http_build_query($option);
        }
    }

    /**
     * 第一步:获取在微信中登录授权的url.
     *
     * @param string $callbackUrl 登录回调地址
     * @param string $state       状态值，不传则自动生成，随后可以通过->state获取。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。一般为每个用户登录时随机生成state存在session中，登录回调中判断state是否和session中相同
     * @param array  $scope       请求用户授权时向用户显示的可进行授权的列表。可空，默认snsapi_userinfo
     *
     * @return string
     */
    public function getWeixinAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'appid'				       => $this->appid,
            'redirect_uri'		  => null === $callbackUrl ? (null === $this->callbackUrl ? (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') : $this->callbackUrl) : $callbackUrl,
            'response_type'		 => 'code',
            'scope'				       => null === $scope ? (null === $this->scope ? 'snsapi_userinfo' : $this->scope) : $scope,
            'state'				       => $this->getState($state),
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getUrl(static::OPEN_DOMAIN . 'connect/oauth2/authorize', $option) . '#wechat_redirect';
        }
        else
        {
            $option['isMp'] = 1;

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
        $this->result = $this->http->get($this->getUrl('sns/oauth2/access_token', [
            'appid'			    => $this->appid,
            'secret'		    => $this->appSecret,
            'code'			     => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'grant_type'	 => 'authorization_code',
        ]))->json(true);
        if (isset($this->result['errcode']) && 0 != $this->result['errcode'])
        {
            throw new ApiException($this->result['errmsg'], $this->result['errcode']);
        }
        else
        {
            switch ((int) $this->openidMode)
            {
                case OpenidMode::OPEN_ID:
                    $this->openid = $this->result['openid'];
                    break;
                case OpenidMode::UNION_ID:
                    $this->openid = $this->result['unionid'];
                    break;
                case OpenidMode::UNION_ID_FIRST:
                    $this->openid = empty($this->result['unionid']) ? $this->result['openid'] : $this->result['unionid'];
                    break;
            }

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
        $this->result = $this->http->get($this->getUrl('sns/userinfo', [
            'access_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
            'openid'		      => $this->openid,
            'lang'			       => $this->lang,
        ]))->json(true);
        if (isset($this->result['errcode']) && 0 != $this->result['errcode'])
        {
            throw new ApiException($this->result['errmsg'], $this->result['errcode']);
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
        $this->result = $this->http->get($this->getUrl('sns/oauth2/refresh_token', [
            'appid'			       => $this->appid,
            'grant_type'	    => 'refresh_token',
            'refresh_token'	 => $refreshToken,
        ]))->json(true);

        return isset($this->result['errcode']) && 0 == $this->result['errcode'];
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
        $this->result = $this->http->get($this->getUrl('sns/auth', [
            'access_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
            'openid'		      => $this->openid,
        ]))->json(true);

        return isset($this->result['errcode']) && 0 == $this->result['errcode'];
    }

    /**
     * 微信小程序登录凭证校验，获取session_key、openid、unionid
     * 返回session_key
     * 调用后可以使用$this->result['openid']或$this->result['unionid']获取相应的值
     *
     * @param string $jsCode
     *
     * @return string
     */
    public function getSessionKey($jsCode)
    {
        $this->result = $this->http->get($this->getUrl('sns/jscode2session', [
            'appid'		    => $this->appid,
            'secret'	    => $this->appSecret,
            'js_code'	   => $jsCode,
            'grant_type' => 'authorization_code',
        ]))->json(true);

        if (isset($this->result['errcode']) && 0 != $this->result['errcode'])
        {
            throw new ApiException($this->result['errmsg'], $this->result['errcode']);
        }
        else
        {
            switch ((int) $this->openidMode)
            {
                case OpenidMode::OPEN_ID:
                    $this->openid = $this->result['openid'];
                    break;
                case OpenidMode::UNION_ID:
                    $this->openid = $this->result['unionid'];
                    break;
                case OpenidMode::UNION_ID_FIRST:
                    $this->openid = empty($this->result['unionid']) ? $this->result['openid'] : $this->result['unionid'];
                    break;
            }
        }

        return $this->result['session_key'];
    }

    /**
     * 解密小程序 wx.getUserInfo() 敏感数据.
     *
     * @param string $encryptedData
     * @param string $iv
     * @param string $sessionKey
     *
     * @return array
     */
    public function descryptData($encryptedData, $iv, $sessionKey)
    {
        if (24 != \strlen($sessionKey))
        {
            throw new \InvalidArgumentException('sessionKey 格式错误');
        }
        if (24 != \strlen($iv))
        {
            throw new \InvalidArgumentException('iv 格式错误');
        }
        $aesKey = base64_decode($sessionKey);
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV);
        if (!$result)
        {
            throw new \InvalidArgumentException('解密失败');
        }
        $dataObj = json_decode($result, true);
        if (!$dataObj)
        {
            throw new \InvalidArgumentException('反序列化数据失败');
        }

        return $dataObj;
    }
}
