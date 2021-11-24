<?php

/**
 * @author Jesse G. Donat <donatj@gmail.com>
 *
 * @link https://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
 * @link https://github.com/donatj/PhpUserAgent
 *
 * @license MIT https://github.com/donatj/PhpUserAgent/blob/master/LICENSE.md
 */

namespace {

	/**
	 * Parses a user agent string into its important parts
	 *
	 * This method is defined for backwards comparability with the old global method.
	 *
	 * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
	 * @return string[] an array with 'browser', 'version' and 'platform' keys
	 * @throws \InvalidArgumentException on not having a proper user agent to parse.
	 *
	 * @deprecated This exists for backwards compatibility with 0.x and will likely be removed in 2.x
	 * @see \donatj\UserAgent\parse_user_agent
	 */
	function parse_user_agent( $u_agent = null ) {
		return \donatj\UserAgent\parse_user_agent($u_agent);
	}
}

namespace donatj\UserAgent {

	const PLATFORM        = 'platform';
	const BROWSER         = 'browser';
	const BROWSER_VERSION = 'version';

	/**
	 * Parses a user agent string into its important parts
	 *
	 * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
	 * @return string[] an array with 'browser', 'version' and 'platform' keys
	 * @throws \InvalidArgumentException on not having a proper user agent to parse.
	 */
	function parse_user_agent( $u_agent = null ) {
		if( $u_agent === null && isset($_SERVER['HTTP_USER_AGENT']) ) {
			$u_agent = (string)$_SERVER['HTTP_USER_AGENT'];
		}

		if( $u_agent === null ) {
			throw new \InvalidArgumentException('parse_user_agent requires a user agent');
		}

		$platform = null;
		$browser  = null;
		$version  = null;

		$return = [ PLATFORM => &$platform, BROWSER => &$browser, BROWSER_VERSION => &$version ];

		if( !$u_agent ) {
			return $return;
		}

		if( preg_match('/\((.*?)\)/m', $u_agent, $parent_matches) ) {
			preg_match_all(<<<'REGEX'
/(?P<platform>BB\d+;|Android|Adr|Symbian|Sailfish|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
(?:\ [^;]*)?
(?:;|$)/imx
REGEX
				, $parent_matches[1], $result);

			$priority = [ 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11', 'Sailfish' ];

			$result[PLATFORM] = array_unique($result[PLATFORM]);
			if( count($result[PLATFORM]) > 1 ) {
				if( $keys = array_intersect($priority, $result[PLATFORM]) ) {
					$platform = reset($keys);
				} else {
					$platform = $result[PLATFORM][0];
				}
			} elseif( isset($result[PLATFORM][0]) ) {
				$platform = $result[PLATFORM][0];
			}
		}

		if( $platform == 'linux-gnu' || $platform == 'X11' ) {
			$platform = 'Linux';
		} elseif( $platform == 'CrOS' ) {
			$platform = 'Chrome OS';
		} elseif( $platform == 'Adr' ) {
			$platform = 'Android';
		}

		preg_match_all(<<<'REGEX'
%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
TizenBrowser|(?:Headless)?Chrome|YaBrowser|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|Edg|CriOS|UCBrowser|Puffin|
OculusBrowser|SamsungBrowser|SailfishBrowser|XiaoMi/MiuiBrowser|
Baiduspider|Applebot|Facebot|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
Valve\ Steam\ Tenfoot|
NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
\)?;?
(?:[:/ ](?P<version>[0-9A-Z.]+)|/[A-Z]*)%ix
REGEX
			, $u_agent, $result);

		// If nothing matched, return null (to avoid undefined index errors)
		if( !isset($result[BROWSER][0]) || !isset($result[BROWSER_VERSION][0]) ) {
			if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result) ) {
				return [ PLATFORM => $platform ?: null, BROWSER => $result[BROWSER], BROWSER_VERSION => empty($result[BROWSER_VERSION]) ? null : $result[BROWSER_VERSION] ];
			}

			return $return;
		}

		if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/i', $u_agent, $rv_result) ) {
			$rv_result = $rv_result[BROWSER_VERSION];
		}

		$browser = $result[BROWSER][0];
		$version = $result[BROWSER_VERSION][0];

		$lowerBrowser = array_map('strtolower', $result[BROWSER]);

		$find = function ( $search, &$key = null, &$value = null ) use ( $lowerBrowser ) {
			$search = (array)$search;

			foreach( $search as $val ) {
				$xkey = array_search(strtolower($val), $lowerBrowser);
				if( $xkey !== false ) {
					$value = $val;
					$key   = $xkey;

					return true;
				}
			}

			return false;
		};

		$findT = function ( array $search, &$key = null, &$value = null ) use ( $find ) {
			$value2 = null;
			if( $find(array_keys($search), $key, $value2) ) {
				$value = $search[$value2];

				return true;
			}

			return false;
		};

		$key = 0;
		$val = '';
		if( $findT([ 'OPR' => 'Opera', 'Facebot' => 'iMessageBot', 'UCBrowser' => 'UC Browser', 'YaBrowser' => 'Yandex', 'Iceweasel' => 'Firefox', 'Icecat' => 'Firefox', 'CriOS' => 'Chrome', 'Edg' => 'Edge', 'XiaoMi/MiuiBrowser' => 'MiuiBrowser' ], $key, $browser) ) {
			$version = is_numeric(substr($result[BROWSER_VERSION][$key], 0, 1)) ? $result[BROWSER_VERSION][$key] : null;
		} elseif( $find('Playstation Vita', $key, $platform) ) {
			$platform = 'PlayStation Vita';
			$browser  = 'Browser';
		} elseif( $find([ 'Kindle Fire', 'Silk' ], $key, $val) ) {
			$browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
			$platform = 'Kindle Fire';
			if( !($version = $result[BROWSER_VERSION][$key]) || !is_numeric($version[0]) ) {
				$version = $result[BROWSER_VERSION][array_search('Version', $result[BROWSER])];
			}
		} elseif( $find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS' ) {
			$browser = 'NintendoBrowser';
			$version = $result[BROWSER_VERSION][$key];
		} elseif( $find('Kindle', $key, $platform) ) {
			$browser = $result[BROWSER][$key];
			$version = $result[BROWSER_VERSION][$key];
		} elseif( $find('Opera', $key, $browser) ) {
			$find('Version', $key);
			$version = $result[BROWSER_VERSION][$key];
		} elseif( $find('Puffin', $key, $browser) ) {
			$version = $result[BROWSER_VERSION][$key];
			if( strlen($version) > 3 ) {
				$part = substr($version, -2);
				if( ctype_upper($part) ) {
					$version = substr($version, 0, -2);

					$flags = [ 'IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows' ];
					if( isset($flags[$part]) ) {
						$platform = $flags[$part];
					}
				}
			}
		} elseif( $find([ 'Applebot', 'IEMobile', 'Edge', 'Midori', 'Vivaldi', 'OculusBrowser', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome', 'HeadlessChrome', 'SailfishBrowser' ], $key, $browser) ) {
			$version = $result[BROWSER_VERSION][$key];
		} elseif( $rv_result && $find('Trident') ) {
			$browser = 'MSIE';
			$version = $rv_result;
		} elseif( $browser == 'AppleWebKit' ) {
			if( $platform == 'Android' ) {
				$browser = 'Android Browser';
			} elseif( strpos($platform, 'BB') === 0 ) {
				$browser  = 'BlackBerry Browser';
				$platform = 'BlackBerry';
			} elseif( $platform == 'BlackBerry' || $platform == 'PlayBook' ) {
				$browser = 'BlackBerry Browser';
			} else {
				$find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
			}

			$find('Version', $key);
			$version = $result[BROWSER_VERSION][$key];
		} elseif( $pKey = preg_grep('/playstation \d/i', $result[BROWSER]) ) {
			$pKey = reset($pKey);

			$platform = 'PlayStation ' . preg_replace('/\D/', '', $pKey);
			$browser  = 'NetFront';
		}

		return $return;
	}
}
