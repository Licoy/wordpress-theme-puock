<?php

namespace Yurun\OAuthLogin\Alipay;

use Yurun\OAuthLogin\ApiException;
use Yurun\OAuthLogin\Base;

/**
 * 支付宝授权
 * 网页授权文档：https://docs.open.alipay.com/53/104114.
 */
class OAuth2 extends Base
{
    /**
     * api域名.
     */
    const API_DOMAIN = 'https://openapi.alipay.com/gateway.do';

    /**
     * 非必须参数。接口权限值，目前只支持 auth_user 和 auth_base 两个值。以空格分隔的权限列表，若不传递此参数，代表请求的数据访问操作权限与上次获取Access Token时一致。通过Refresh Token刷新Access Token时所要求的scope权限范围必须小于等于上次获取Access Token时授予的权限范围。
     *
     * @var string
     */
    public $scope;

    /**
     * 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2.
     *
     * @var string
     */
    public $signType = 'RSA2';

    /**
     * 详见应用授权概述:https://opendocs.alipay.com/isv/10467/xldcyq.
     *
     * @var string
     */
    public $appAuthToken;

    /**
     * 私有证书文件内容.
     *
     * @var string
     */
    public $appPrivateKey;

    /**
     * 私有证书文件地址，不为空时优先使用文件地址
     *
     * @var string
     */
    public $appPrivateKeyFile;

    /**
     * 第一步:获取登录页面跳转url.
     *
     * @param string $callbackUrl 登录回调地址
     * @param string $state       非必须参数，用于保持请求和回调的状态，授权服务器在回调时（重定向用户浏览器到“redirect_uri”时），会在Query Parameter中原样回传该参数。OAuth2.0标准协议建议，利用state参数来防止CSRF攻击。
     * @param array  $scope       非必须参数，以空格分隔的权限列表，若不传递此参数，代表请求用户的默认权限。
     *
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = [
            'app_id'			      => $this->appid,
            'scope'				      => $scope ? $scope : 'auth_user',
            'redirect_uri'		 => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'state'				      => $this->getState($state),
        ];
        if (null === $this->loginAgentUrl)
        {
            return 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?' . $this->http_build_query($option);
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
        $params = [
            'app_id'		    => $this->appid,
            'method'		    => 'alipay.system.oauth.token',
            'charset'		   => 'utf-8',
            'sign_type'		 => $this->signType,
            'timestamp'		 => date('Y-m-d H:i:s'),
            'version'		   => '1.0',
            'grant_type'	 => 'authorization_code',
            'code'			     => isset($code) ? $code : (isset($_GET['auth_code']) ? $_GET['auth_code'] : ''),
        ];
        if ($this->appAuthToken)
        {
            $params['app_auth_token'] = $this->appAuthToken;
        }
        $params['sign'] = $this->sign($params);
        $response = $this->http->get(static::API_DOMAIN, $params);
        $this->result = $response->json(true);

        if (!isset($this->result['alipay_system_oauth_token_response']) && isset($this->result['error_response']))
        {
            throw new ApiException(sprintf('%s %s', $this->result['error_response']['msg'], $this->result['error_response']['sub_msg']), $this->result['error_response']['code']);
        }
        $this->result = $responseData = $this->result['alipay_system_oauth_token_response'];
        if (isset($responseData['code']))
        {
            throw new ApiException(sprintf('%s %s', $responseData['msg'], $responseData['sub_msg']), $responseData['code']);
        }
        $this->openid = $responseData['user_id'];

        return $this->accessToken = $responseData['access_token'];
    }

    /**
     * 获取用户资料.
     *
     * @param string $accessToken
     * @link https://opendocs.alipay.com/apis/api_2/alipay.user.info.share
     *
     * @return array
     */
    public function getUserInfo($accessToken = null)
    {
        $params = [
            'app_id'		    => $this->appid,
            'method'		    => 'alipay.user.info.share',
            'charset'		   => 'utf-8',
            'sign_type'		 => $this->signType,
            'timestamp'		 => date('Y-m-d H:i:s'),
            'version'		   => '1.0',
            'auth_token'	 => null === $accessToken ? $this->accessToken : $accessToken,
        ];
        if ($this->appAuthToken)
        {
            $params['app_auth_token'] = $this->appAuthToken;
        }
        $params['sign'] = $this->sign($params);
        $response = $this->http->get(static::API_DOMAIN, $params);
        $this->result = $response->json(true);

        if (!isset($this->result['alipay_user_info_share_response']) && isset($this->result['error_response']))
        {
            throw new ApiException(sprintf('%s %s', $this->result['error_response']['msg'], $this->result['error_response']['sub_msg']), $this->result['error_response']['code']);
        }
        $this->result = $responseData = $this->result['alipay_user_info_share_response'];
        if (isset($responseData['code']) && 10000 != $responseData['code'])
        {
            throw new ApiException(sprintf('%s %s', $responseData['msg'], $responseData['sub_msg']), $responseData['code']);
        }

        return $responseData;
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
        $params = [
            'app_id'		       => $this->appid,
            'method'		       => 'alipay.system.oauth.token',
            'charset'		      => 'utf-8',
            'sign_type'		    => $this->signType,
            'timestamp'		    => date('Y-m-d H:i:s'),
            'version'		      => '1.0',
            'grant_type'	    => 'refresh_token',
            'refresh_token'	 => $refreshToken,
        ];
        if ($this->appAuthToken)
        {
            $params['app_auth_token'] = $this->appAuthToken;
        }
        $params['sign'] = $this->sign($params);
        $response = $this->http->get(static::API_DOMAIN, $params);
        $this->result = $response->json(true);

        if (!isset($this->result['alipay_system_oauth_token_response']) && isset($this->result['error_response']))
        {
            throw new ApiException(sprintf('%s %s', $this->result['error_response']['msg'], $this->result['error_response']['sub_msg']), $this->result['error_response']['code']);
        }
        $this->result = $responseData = $this->result['alipay_system_oauth_token_response'];
        if (isset($responseData['code']))
        {
            throw new ApiException(sprintf('%s %s', $responseData['msg'], $responseData['sub_msg']), $responseData['code']);
        }
        $this->openid = $responseData['user_id'];

        return $this->accessToken = $responseData['access_token'];
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

    /**
     * 签名.
     *
     * @param $data
     *
     * @return string
     */
    public function sign($data)
    {
        $content = $this->parseSignData($data);
        if (empty($this->appPrivateKeyFile))
        {
            $key = $this->appPrivateKey;
            $method = 'signPrivate';
        }
        else
        {
            $key = $this->appPrivateKeyFile;
            $method = 'signPrivateFromFile';
        }
        switch ($this->signType)
        {
            case 'RSA':
                $result = \Yurun\OAuthLogin\Lib\RSA::$method($content, $key);
                break;
            case 'RSA2':
                $result = \Yurun\OAuthLogin\Lib\RSA2::$method($content, $key);
                break;
            default:
                throw new \Exception('未知的加密方式：' . $this->signType);
        }

        return base64_encode($result);
    }

    /**
     * 处理验证数据.
     *
     * @param array $data
     *
     * @return string
     */
    public function parseSignData($data)
    {
        if (isset($data['sign']))
        {
            unset($data['sign']);
        }
        ksort($data);
        $content = '';
        foreach ($data as $k => $v)
        {
            if ('' !== $v && null !== $v && !\is_array($v))
            {
                $content .= $k . '=' . $v . '&';
            }
        }

        return trim($content, '&');
    }
}
