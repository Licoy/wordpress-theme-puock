<?php

namespace Yurun\OAuthLogin\QQ;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

class OAuth2 extends Base
{
    /**
     * api接口域名.
     */
    const API_DOMAIN = 'https://graph.qq.com/';

    /**
     * 仅PC网站接入时使用。用于展示的样式。不传则默认展示为PC下的样式。如果传入“mobile”，则展示为mobile端下的样式。
     *
     * @var string
     */
    public $display;

    /**
     * openid从哪个字段取，默认为openid.
     *
     * @var int
     */
    public $openidMode = OpenidMode::OPEN_ID;

    /**
     * 是否使用unionid，默认为false.
     *
     * @var bool
     */
    public $isUseUnionID = false;

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
            'response_type'		 => 'code',
            'client_id'			    => $this->appid,
            'redirect_uri'		  => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'state'				       => $this->getState($state),
            'scope'				       => null === $scope ? $this->scope : $scope,
            'display'			      => $this->display,
        ];
        if (null === $this->loginAgentUrl)
        {
            return $this->getUrl('oauth2.0/authorize', $option);
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
        $this->result = $this->http->get(
            $this->getUrl('oauth2.0/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $this->appid,
                'client_secret' => $this->appSecret,
                'code' => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
                'state' => isset($state) ? $state : (isset($_GET['state']) ? $_GET['state'] : ''),
                'redirect_uri' => $this->getRedirectUri(),
                'fmt' => 'json',
            ])
        )->json(true);
        if (isset($this->result['error'])) {
            throw new ApiException($this->result['error_description'], $this->result['error']);
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
        if (null === $this->openid)
        {
            $this->getOpenID($accessToken);
        }
        $this->result = $this->http->get($this->getUrl('user/get_user_info', [
            'access_token'			     => null === $accessToken ? $this->accessToken : $accessToken,
            'oauth_consumer_key'	 => $this->appid,
            'openid'				          => $this->openid,
        ]))->json(true);
        if (isset($this->result['ret']) && 0 != $this->result['ret'])
        {
            throw new ApiException($this->result['msg'], $this->result['ret']);
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
        $this->result = $this->http->get($this->getUrl('oauth2.0/token', [
            'grant_type'	    => 'refresh_token',
            'client_id'		    => $this->appid,
            'client_secret'	 => $this->appSecret,
            'refresh_token'	 => $refreshToken,
        ]))->jsonp(true);

        return isset($this->result['code']) && 0 == $this->result['code'];
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
            $this->getOpenID($accessToken);

            return true;
        }
        catch (ApiException $e)
        {
            return false;
        }
    }

    /**
     * 获取OpenID.
     *
     * @param string $accessToken
     *
     * @return string
     */
    public function getOpenID($accessToken = null)
    {
        $params = [
            'access_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
        ];
        if ($this->isUseUnionID && OpenidMode::UNION_ID === $this->openidMode)
        {
            $params['unionid'] = $this->openidMode;
        }
        $this->result = $this->http->get($this->getUrl('oauth2.0/me', $params))->jsonp(true);
        if (isset($this->result['error']))
        {
            throw new ApiException($this->result['error_description'], $this->result['error']);
        }
        else
        {
            $this->openid = $this->result['openid'];
            if ($this->isUseUnionID && OpenidMode::UNION_ID === $this->openidMode)
            {
                return $this->result['unionid'];
            }
            else
            {
                return $this->openid;
            }
        }
    }

    /**
     * QQ小程序登录凭证校验，获取session_key、openid、unionid
     * 返回session_key
     * 调用后可以使用$this->result['openid']或$this->result['unionid']获取相应的值
     *
     * @param string $jsCode
     *
     * @return string
     */
    public function getSessionKey($jsCode)
    {
        $this->result = $this->http->get('https://api.q.qq.com/sns/jscode2session', [
            'appid'		    => $this->appid,
            'secret'	    => $this->appSecret,
            'js_code'	   => $jsCode,
            'grant_type' => 'authorization_code',
        ])->json(true);

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
     * 解密小程序 qq.getUserInfo() 敏感数据.
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
        $dataObj = json_decode($result, true);
        if (!$dataObj)
        {
            throw new \InvalidArgumentException('反序列化数据失败');
        }

        return $dataObj;
    }
}
