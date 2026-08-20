<?php

namespace YahnisElsts\PluginUpdateChecker\v5p7\Vcs;

use Parsedown;
use PucReadmeParser;

if ( !class_exists(Api::class, false) ):

	abstract class Api {
		const STRATEGY_LATEST_RELEASE = 'latest_release';
		const STRATEGY_LATEST_TAG = 'latest_tag';
		const STRATEGY_STABLE_TAG = 'stable_tag';
		const STRATEGY_BRANCH = 'branch';

		/**
		 * Consider all releases regardless of their version number or prerelease/upcoming
		 * release status.
		 */
		const RELEASE_FILTER_ALL = 3;

		/**
		 * Exclude releases that have the "prerelease" or "upcoming release" flag.
		 *
		 * This does *not* look for prerelease keywords like "beta" in the version number.
		 * It only uses the data provided by the API. For example, on GitHub, you can
		 * manually mark a release as a prerelease.
		 */
		const RELEASE_FILTER_SKIP_PRERELEASE = 1;

		/**
		 * If there are no release assets or none of them match the configured filter,
		 * fall back to the automatically generated source code archive.
		 */
		const PREFER_RELEASE_ASSETS = 1;
		/**
		 * Skip releases that don't have any matching release assets.
		 */
		const REQUIRE_RELEASE_ASSETS = 2;

		protected $tagNameProperty = 'name';
		protected $slug = '';

		/**
		 * @var string
		 */
		protected $repositoryUrl = '';

		/**
		 * @var mixed Authentication details for private repositories. Format depends on service.
		 */
		protected $credentials = null;

		/**
		 * @var string|null The value of the "Authorization" header for API requests.
		 */
		private $authorizationHeader = null;

		/**
		 * @var string|null If set, add the "Authorization" header to update downloads that start with this prefix.
		 */
		private $downloadUrlPrefixRequiringAuth = null;

		/**
		 * @var string The filter tag that's used to filter options passed to wp_remote_get.
		 * For example, "puc_request_info_options-slug" or "puc_request_update_options_theme-slug".
		 */
		protected $httpFilterName = '';

		/**
		 * @var string The filter applied to the list of update detection strategies that
		 * are used to find the latest version.
		 */
		protected $strategyFilterName = '';

		/**
		 * @var string|null
		 */
		protected $localDirectory = null;

		/**
		 * Api constructor.
		 *
		 * @param string $repositoryUrl
		 * @param array|string|null $credentials
		 */
		public function __construct($repositoryUrl, $credentials = null) {
			$this->repositoryUrl = $repositoryUrl;
			$this->setAuthentication($credentials);
		}

		/**
		 * @return string
		 */
		public function getRepositoryUrl() {
			return $this->repositoryUrl;
		}

		/**
		 * Figure out which reference (i.e. tag or branch) contains the latest version.
		 *
		 * @param string $configBranch Start looking in this branch.
		 * @return null|Reference
		 */
		public function chooseReference($configBranch) {
			$strategies = $this->getUpdateDetectionStrategies($configBranch);

			if ( !empty($this->strategyFilterName) ) {
				$strategies = apply_filters(
					$this->strategyFilterName,
					$strategies,
					$this->slug
				);
			}

			foreach ($strategies as $strategy) {
				$reference = call_user_func($strategy);
				if ( !empty($reference) ) {
					return $reference;
				}
			}
			return null;
		}

		/**
		 * Get an ordered list of strategies that can be used to find the latest version.
		 *
		 * The update checker will try each strategy in order until one of them
		 * returns a valid reference.
		 *
		 * @param string $configBranch
		 * @return array<callable> Array of callables that return Vcs_Reference objects.
		 */
		abstract protected function getUpdateDetectionStrategies($configBranch);

		/**
		 * Get the readme.txt file from the remote repository and parse it
		 * according to the plugin readme standard.
		 *
		 * @param string $ref Tag or branch name.
		 * @return array Parsed readme.
		 */
		public function getRemoteReadme($ref = 'master') {
			$fileContents = $this->getRemoteFile($this->getLocalReadmeName(), $ref);
			if ( empty($fileContents) ) {
				return array();
			}

			$parser = new PucReadmeParser();
			return $parser->parse_readme_contents($fileContents);
		}

		/**
		 * Get the case-sensitive name of the local readme.txt file.
		 *
		 * In most cases it should just be called "readme.txt", but some plugins call it "README.txt",
		 * "README.TXT", or even "Readme.txt". Most VCS are case-sensitive so we need to know the correct
		 * capitalization.
		 *
		 * Defaults to "readme.txt" (all lowercase).
		 *
		 * @return string
		 */
		public function getLocalReadmeName() {
			static $fileName = null;
			if ( $fileName !== null ) {
				return $fileName;
			}

			$fileName = 'readme.txt';
			if ( isset($this->localDirectory) ) {
				$files = scandir($this->localDirectory);
				if ( !empty($files) ) {
					foreach ($files as $possibleFileName) {
						if ( strcasecmp($possibleFileName, 'readme.txt') === 0 ) {
							$fileName = $possibleFileName;
							break;
						}
					}
				}
			}
			return $fileName;
		}

		/**
		 * Get a branch.
		 *
		 * @param string $branchName
		 * @return Reference|null
		 */
		abstract public function getBranch($branchName);

		/**
		 * Get a specific tag.
		 *
		 * @param string $tagName
		 * @return Reference|null
		 */
		abstract public function getTag($tagName);

		/**
		 * Get the tag that looks like the highest version number.
		 * (Implementations should skip pre-release versions if possible.)
		 *
		 * @return Reference|null
		 */
		abstract public function getLatestTag();

		/**
		 * Check if a tag name string looks like a version number.
		 *
		 * @param string $name
		 * @return bool
		 */
		protected function looksLikeVersion($name) {
			//Tag names may be prefixed with "v", e.g. "v1.2.3".
			$name = ltrim($name, 'v');

			//The version string must start with a number.
			if ( !is_numeric(substr($name, 0, 1)) ) {
				return false;
			}

			//The goal is to accept any SemVer-compatible or "PHP-standardized" version number.
			return (preg_match('@^(\d{1,5}?)(\.\d{1,10}?){0,4}?($|[abrdp+_\-]|\s)@i', $name) === 1);
		}

		/**
		 * Check if a tag appears to be named like a version number.
		 *
		 * @param \stdClass $tag
		 * @return bool
		 */
		protected function isVersionTag($tag) {
			$property = $this->tagNameProperty;
			return isset($tag->$property) && $this->looksLikeVersion($tag->$property);
		}

		/**
		 * Sort a list of tags as if they were version numbers.
		 * Tags that don't look like version number will be removed.
		 *
		 * @param \stdClass[] $tags Array of tag objects.
		 * @return \stdClass[] Filtered array of tags sorted in descending order.
		 */
		protected function sortTagsByVersion($tags) {
			//Keep only those tags that look like version numbers.
			$versionTags = array_filter($tags, array($this, 'isVersionTag'));
			//Sort them in descending order.
			usort($versionTags, array($this, 'compareTagNames'));

			return $versionTags;
		}

		/**
		 * Compare two tags as if they were version number.
		 *
		 * @param \stdClass $tag1 Tag object.
		 * @param \stdClass $tag2 Another tag object.
		 * @return int
		 */
		protected function compareTagNames($tag1, $tag2) {
			$property = $this->tagNameProperty;
			if ( !isset($tag1->$property) ) {
				return 1;
			}
			if ( !isset($tag2->$property) ) {
				return -1;
			}
			return -version_compare(ltrim($tag1->$property, 'v'), ltrim($tag2->$property, 'v'));
		}

		/**
		 * Get the contents of a file from a specific branch or tag.
		 *
		 * @param string $path File name.
		 * @param string $ref
		 * @return null|string Either the contents of the file, or null if the file doesn't exist or there's an error.
		 */
		abstract public function getRemoteFile($path, $ref = 'master');

		/**
		 * Get the timestamp of the latest commit that changed the specified branch or tag.
		 *
		 * @param string $ref Reference name (e.g. branch or tag).
		 * @return string|null
		 */
		abstract public function getLatestCommitTime($ref);

		/**
		 * Get the contents of the changelog file from the repository.
		 *
		 * @param string $ref
		 * @param string $localDirectory Full path to the local plugin or theme directory.
		 * @return null|string The HTML contents of the changelog.
		 */
		public function getRemoteChangelog($ref, $localDirectory) {
			$filename = $this->findChangelogName($localDirectory);
			if ( empty($filename) ) {
				return null;
			}

			$changelog = $this->getRemoteFile($filename, $ref);
			if ( $changelog === null ) {
				return null;
			}

			return Parsedown::instance()->text($changelog);
		}

		/**
		 * Guess the name of the changelog file.
		 *
		 * @param string $directory
		 * @return string|null
		 */
		protected function findChangelogName($directory = null) {
			if ( !isset($directory) ) {
				$directory = $this->localDirectory;
			}
			if ( empty($directory) || !is_dir($directory) || ($directory === '.') ) {
				return null;
			}

			$possibleNames = array('CHANGES.md', 'CHANGELOG.md', 'changes.md', 'changelog.md');
			$files = scandir($directory);
			$foundNames = array_intersect($possibleNames, $files);

			if ( !empty($foundNames) ) {
				return reset($foundNames);
			}
			return null;
		}

		/**
		 * @return array
		 */
		protected function getApiRequestHttpOptions() {
			$options = ['timeout' => wp_doing_cron() ? 10 : 3];
			if ( $this->isAuthenticationEnabled() && !empty($this->authorizationHeader) ) {
				$options['headers'] = ['Authorization' => $this->authorizationHeader];
			}

			if ( !empty($this->httpFilterName) ) {
				$options = apply_filters($this->httpFilterName, $options);
			}

			return $options;
		}

		/**
		 * Set authentication credentials.
		 *
		 * @param $credentials
		 */
		public function setAuthentication($credentials) {
			$this->credentials = $credentials;
		}

		public function isAuthenticationEnabled() {
			return !empty($this->credentials) || !empty($this->authorizationHeader);
		}

		/**
		 * Get the value of the "Authorization" header for API requests, if any.
		 *
		 * @return string
		 */
		protected function getAuthorizationHeader() {
			return $this->authorizationHeader ?: '';
		}

		/**
		 * Enable basic access authentication with the specified username and password.
		 *
		 * @param string $username
		 * @param string $password
		 * @param string|null $downloadUrlPrefix Optionally, add the same Authorization header to update
		 *  downloads where the download URL starts with this prefix.
		 */
		protected function enableBasicAuth($username, $password, $downloadUrlPrefix = null) {
			$this->authorizationHeader = 'Basic ' . base64_encode($username . ':' . $password);

			$this->downloadUrlPrefixRequiringAuth = $downloadUrlPrefix;
			if ( !empty($this->downloadUrlPrefixRequiringAuth) ) {
				$this->enableDownloadRequestFilter();
			}
		}

		/**
		 * @var bool Whether the hook that registers download request filter(s) has already been added.
		 */
		private $preDownloadHookAdded = false;

		/**
		 * Enable the hooks that let you filter HTTP requests for update downloads.
		 */
		protected function enableDownloadRequestFilter() {
			if ( $this->preDownloadHookAdded ) {
				return;
			}
			$this->preDownloadHookAdded = true;

			//Optimization: Instead of filtering all HTTP requests, let's do it only when
			//WordPress is about to download an update. So this is a two-step process;
			//the actual request arg filter is added in the following hook.
			add_filter('upgrader_pre_download', [$this, 'addDownloadRequestFilters']); //WP 3.7+
		}

		/**
		 * @var bool
		 */
		private $downloadFiltersAdded = false;

		/**
		 * @internal
		 * @param bool $result Pass-through value for the "upgrader_pre_download" filter. Ignored by this callback.
		 * @return bool
		 */
		public function addDownloadRequestFilters($result) {
			if ( !$this->downloadFiltersAdded ) {
				$this->downloadFiltersAdded = true;

				//phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.http_request_args -- The callback doesn't change the timeout.
				add_filter('http_request_args', [$this, 'filterUpdateDownloadRequestArgs'], 10, 2);

				$authorizationHeader = $this->getAuthorizationHeader();
				if ( $this->isAuthenticationEnabled() && !empty($authorizationHeader) ) {
					add_action('requests-requests.before_redirect', [$this, 'removeAuthHeaderFromRedirects'], 10, 2);
				}
			}
			return $result;
		}

		/**
		 * Filter request arguments/options for update downloads.
		 *
		 * Note that this callback can potentially be called for *any* update download. You still
		 * need to verify that the URL is one of yours.
		 *
		 * @internal
		 * @param array $requestArgs
		 * @param string $url
		 * @return array
		 */
		public function filterUpdateDownloadRequestArgs($requestArgs, $url = '') {
			//Add an authorization header to our downloads if needed.
			$authHeader = $this->getAuthorizationHeader();
			if (
				$this->isAuthenticationEnabled()
				&& !empty($authHeader)
				&& !empty($this->downloadUrlPrefixRequiringAuth)
				&& ((strpos($url, $this->downloadUrlPrefixRequiringAuth)) === 0)
			) {
				$requestArgs['headers']['Authorization'] = $authHeader;
			}
			return $requestArgs;
		}

		/**
		 * At least in older WP versions, when following a redirect, the Requests library will
		 * automatically forward the Authorization header to other hosts. We don't want that
		 * because it breaks AWS downloads and can leak authorization information.
		 *
		 * @param string $location
		 * @param array $headers
		 * @internal
		 */
		public function removeAuthHeaderFromRedirects(&$location, &$headers) {
			if (
				//If there's no download URL prefix configured, we would not have added an auth header,
				//and there's also no way to check if this URL needs auth or not.
				empty($this->downloadUrlPrefixRequiringAuth)
				//If this request goes to our download URL, we can leave the header.
				|| ((strpos($location, $this->downloadUrlPrefixRequiringAuth)) === 0)
			) {
				return;
			}

			//Remove the header.
			$authorizationHeader = $this->getAuthorizationHeader();
			if ( isset($headers['Authorization']) && ($headers['Authorization'] === $authorizationHeader) ) {
				unset($headers['Authorization']);
			}
		}

		/**
		 * @param string $url
		 * @return string
		 */
		public function signDownloadUrl($url) {
			return $url;
		}

		/**
		 * @param string $filterName
		 */
		public function setHttpFilterName($filterName) {
			$this->httpFilterName = $filterName;
		}

		/**
		 * @param string $filterName
		 */
		public function setStrategyFilterName($filterName) {
			$this->strategyFilterName = $filterName;
		}

		/**
		 * @param string $directory
		 */
		public function setLocalDirectory($directory) {
			if ( empty($directory) || !is_dir($directory) || ($directory === '.') ) {
				$this->localDirectory = null;
			} else {
				$this->localDirectory = $directory;
			}
		}

		/**
		 * @param string $slug
		 */
		public function setSlug($slug) {
			$this->slug = $slug;
		}
	}

endif;
