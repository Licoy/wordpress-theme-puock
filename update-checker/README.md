Plugin Update Checker
=====================

This is a custom update checker library for WordPress plugins and themes. It lets you add automatic update notifications and one-click upgrades to your commercial plugins, private themes, and so on. All you need to do is put your plugin/theme details in a JSON file, place the file on your server, and pass the URL to the library. The library periodically checks the URL to see if there's a new version available and displays an update notification to the user if necessary.

From the users' perspective, it works just like with plugins and themes hosted on WordPress.org. The update checker uses the default upgrade UI that is familiar to most WordPress users.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Getting Started](#getting-started)
  - [Self-hosted Plugins and Themes](#self-hosted-plugins-and-themes)
    - [How to Release an Update](#how-to-release-an-update)
    - [Notes](#notes)
  - [GitHub Integration](#github-integration)
    - [How to Release an Update](#how-to-release-an-update-1)
    - [Notes](#notes-1)
  - [BitBucket Integration](#bitbucket-integration)
    - [How to Release an Update](#how-to-release-an-update-2)
  - [GitLab Integration](#gitlab-integration)
    - [How to Release a GitLab Update](#how-to-release-a-gitlab-update)
- [Migrating from 4.x](#migrating-from-4x)
- [License Management](#license-management)
- [Resources](#resources)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

Getting Started
---------------

*Note:* In each of the below examples, part of the instructions are to create an instance of the update checker class. It's recommended to do this either during the `plugins_loaded` action or outside of any hooks. If you do it only during an `admin_*` action, then updates will not be visible to a wide variety of WordPress maanagement tools; they will only be visible to logged-in users on dashboard pages.

### Self-hosted Plugins and Themes

1. Download [the latest release](https://github.com/YahnisElsts/plugin-update-checker/releases/latest) and copy the `plugin-update-checker` directory to your plugin or theme.
2. Go to the `examples` subdirectory and open the .json file that fits your project type. Replace the placeholder data with your plugin/theme details. 
	- Plugin example:
	
		```json
		{
			"name" : "Plugin Name",
			"version" : "2.0",
			"download_url" : "https://example.com/plugin-name-2.0.zip",
			"sections" : {
				"description" : "Plugin description here. You can use HTML."
			}
		}
		```
		
        This is a minimal example that leaves out optional fields. See [this table](https://docs.google.com/spreadsheets/d/1eOBbW7Go2qEQXReOOCdidMTf_tDYRq4JfegcO1CBPIs/edit?usp=sharing) for a full list of supported fields and their descriptions.
	- Theme example:
	
		```json
		{
			"version": "2.0",
			"details_url": "https://example.com/version-2.0-details.html",
			"download_url": "https://example.com/example-theme-2.0.zip"
		}
		```
		
		This is actually a complete example that shows all theme-related fields. `version` and `download_url` should be self-explanatory. The `details_url` key specifies the page that the user will see if they click the "View version 1.2.3 details" link in an update notification.  
3. Upload the JSON file to a publicly accessible location.
4. Add the following code to the main plugin file or to the `functions.php` file:

	```php
	require 'path/to/plugin-update-checker/plugin-update-checker.php';
	use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
 
	$myUpdateChecker = PucFactory::buildUpdateChecker(
		'https://example.com/path/to/details.json',
		__FILE__, //Full path to the main plugin file or functions.php.
		'unique-plugin-or-theme-slug'
	);
	```
	Note: If you're using the Composer autoloader, you don't need to explicitly `require` the library.

#### How to Release an Update

Change the `version` number in the JSON file and make sure that `download_url` points to the latest version. Update the other fields if necessary. Tip: You can use [wp-update-server](https://github.com/YahnisElsts/wp-update-server) to automate this process.

By default, the library will check the specified URL for changes every 12 hours. You can force it to check immediately by clicking the "Check for updates" link on the "Plugins" page (it's next to the "Visit plugin site" link). Themes don't have that link, but you can also trigger an update check like this:
 
 1. Install [Debug Bar](https://srd.wordpress.org/plugins/debug-bar/).
 2. Click the "Debug" menu in the Admin Bar (a.k.a Toolbar).
 3. Open the "PUC (your-slug)" panel. 
 4. Click the "Check Now" button.  

#### Notes
- The second argument passed to `buildUpdateChecker` must be the absolute path to the main plugin file or any file in the theme directory. If you followed the "getting started" instructions, you can just use the `__FILE__` constant.
- The third argument - i.e. the slug - is optional but recommended. In most cases, the slug should be the same as the name of your plugin directory. For example, if your plugin lives in `/wp-content/plugins/my-plugin`, set the slug to `my-plugin`. If the slug is omitted, the update checker will use the name of the main plugin file as the slug (e.g. `my-cool-plugin.php` &rarr; `my-cool-plugin`). This can lead to conflicts if your plugin has a generic file name like `plugin.php`. 
	
	This doesn't affect themes because PUC uses the theme directory name as the default slug. Still, if you're planning to use the slug in your own code - e.g. to filter updates or override update checker behaviour - it can be a good idea to set it explicitly. 

### GitHub Integration

1. Download [the latest release](https://github.com/YahnisElsts/plugin-update-checker/releases/latest) and copy the `plugin-update-checker` directory to your plugin or theme.
2. Add the following code to the main plugin file or `functions.php`:

	```php
	require 'plugin-update-checker/plugin-update-checker.php';
	use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
 
	$myUpdateChecker = PucFactory::buildUpdateChecker(
		'https://github.com/user-name/repo-name/',
		__FILE__,
		'unique-plugin-or-theme-slug'
	);
	
	//Set the branch that contains the stable release.
	$myUpdateChecker->setBranch('stable-branch-name');

	//Optional: If you're using a private repository, specify the access token like this:
	$myUpdateChecker->setAuthentication('your-token-here');
	```
3. Plugins only: Add a `readme.txt` file formatted according to the [WordPress.org plugin readme standard](https://wordpress.org/plugins/readme.txt) to your repository. The contents of this file will be shown when the user clicks the "View version 1.2.3 details" link.

#### How to Release an Update

This library supports a couple of different ways to release updates on GitHub. Pick the one that best fits your workflow.

- **GitHub releases** 
	
	Create a new release using the "Releases" feature on GitHub. The tag name and release title don't matter. The description is optional, but if you do provide one, it will be displayed when the user clicks the "View version x.y.z details" link on the "Plugins" page. Note that PUC ignores releases marked as "This is a pre-release".
	
	If you want to use release assets, call the `enableReleaseAssets()` method after creating the update checker instance:
	```php
	$myUpdateChecker->getVcsApi()->enableReleaseAssets();
	```
	
- **Tags** 
	
	To release version 1.2.3, create a new Git tag named `v1.2.3` or `1.2.3`. That's it.
	
	PUC doesn't require strict adherence to [SemVer](https://semver.org/). These are all valid tag names: `v1.2.3`, `v1.2-foo`, `1.2.3_rc1-ABC`, `1.2.3.4.5`. However, be warned that it's not smart enough to filter out alpha/beta/RC versions. If that's a problem, you might want to use GitHub releases or branches instead.
	
- **Stable branch** 
	
	Point the update checker at a stable, production-ready branch: 
	 ```php
	 $updateChecker->setBranch('branch-name');
	 ```
	 PUC will periodically check the `Version` header in the main plugin file or `style.css` and display a notification if it's greater than the installed version.
	 
	 Caveat: If you set the branch to `master` (the default), the update checker will look for recent releases and tags first. It'll only use the `master` branch if it doesn't find anything else suitable.

#### Notes

The library will pull update details from the following parts of a release/tag/branch:

- Version number
	- The "Version" plugin header.
	- The latest GitHub release or tag name.
- Changelog
	- The "Changelog" section of `readme.txt`.
	- One of the following files:
		CHANGES.md, CHANGELOG.md, changes.md, changelog.md
	- GitHub release notes.
- Required and tested WordPress versions
	- The "Requires at least" and "Tested up to" fields in `readme.txt`.
	- The following plugin headers:
		`Required WP`, `Tested WP`, `Requires at least`, `Tested up to`
- "Last updated" timestamp
	- The creation timestamp of the latest GitHub release.
	- The latest commit in the selected tag or branch.
- Number of downloads
	- The `download_count` statistic of the latest release.
	- If you're not using GitHub releases, there will be no download stats.
- Other plugin details - author, homepage URL, description
	- The "Description" section of `readme.txt`.
	- Remote plugin headers (i.e. the latest version on GitHub).
	- Local plugin headers (i.e. the currently installed version).
- Ratings, banners, screenshots
	- Not supported.
	
### BitBucket Integration

1. Download [the latest release](https://github.com/YahnisElsts/plugin-update-checker/releases/latest) and copy the `plugin-update-checker` directory to your plugin or theme.
2. Add the following code to the main plugin file or `functions.php`:

	```php
	require 'plugin-update-checker/plugin-update-checker.php';
	use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
 
	$myUpdateChecker = PucFactory::buildUpdateChecker(
		'https://bitbucket.org/user-name/repo-name',
		__FILE__,
		'unique-plugin-or-theme-slug'
	);

	//Optional: If you're using a private repository, create an OAuth consumer
	//and set the authentication credentials like this:
	//Note: For now you need to check "This is a private consumer" when
	//creating the consumer to work around #134:
	// https://github.com/YahnisElsts/plugin-update-checker/issues/134
	$myUpdateChecker->setAuthentication(array(
		'consumer_key' => '...',
		'consumer_secret' => '...',
	));

	//Optional: Set the branch that contains the stable release.
	$myUpdateChecker->setBranch('stable-branch-name');
	```
3. Optional: Add a `readme.txt` file formatted according to the [WordPress.org plugin readme standard](https://wordpress.org/plugins/readme.txt) to your repository. For plugins, the contents of this file will be shown when the user clicks the "View version 1.2.3 details" link.

#### How to Release an Update

BitBucket doesn't have an equivalent to GitHub's releases, so the process is slightly different. You can use any of the following approaches: 

- **`Stable tag` header** 
	
	This is the recommended approach if you're using tags to mark each version. Add a `readme.txt` file formatted according to the [WordPress.org plugin readme standard](https://wordpress.org/plugins/readme.txt) to your repository. Set the "stable tag" header to the tag that represents the latest release. Example:
	```text
	Stable tag: v1.2.3
	```
	The tag doesn't have to start with a "v" or follow any particular format. You can use any name you like as long as it's a valid Git tag.
	
	Tip: If you explicitly set a stable branch, the update checker will look for a `readme.txt` in that branch. Otherwise it will only look at the `master` branch.
	
- **Tags** 
	
	You can skip the "stable tag" bit and just create a new Git tag named `v1.2.3` or `1.2.3`. The update checker will look at the most recent tags and pick the one that looks like the highest version number.
	
	PUC doesn't require strict adherence to [SemVer](https://semver.org/). These are all valid tag names: `v1.2.3`, `v1.2-foo`, `1.2.3_rc1-ABC`, `1.2.3.4.5`. However, be warned that it's not smart enough to filter out alpha/beta/RC versions.
	
- **Stable branch** 
	
	Point the update checker at a stable, production-ready branch: 
	 ```php
	 $updateChecker->setBranch('branch-name');
	 ```
	 PUC will periodically check the `Version` header in the main plugin file or `style.css` and display a notification if it's greater than the installed version. Caveat: If you set the branch to `master`, the update checker will still look for tags first.

### GitLab Integration

1. Download [the latest release](https://github.com/YahnisElsts/plugin-update-checker/releases/latest) and copy the `plugin-update-checker` directory to your plugin or theme.
2. Add the following code to the main plugin file or `functions.php` and define how you want to check for updates from Gitlab (refer to: [Gitlab: How to Release an Update](#how-to-release-a-gitlab-update)):

	```php
	require 'plugin-update-checker/plugin-update-checker.php';
	use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

	$myUpdateChecker = PucFactory::buildUpdateChecker(
		'https://gitlab.com/user-name/repo-name/',
		__FILE__,
		'unique-plugin-or-theme-slug'
	);

	//Optional: If you're using a private repository, specify the access token like this:
	$myUpdateChecker->setAuthentication('your-token-here');
	```

	Alternatively, if you're using a self-hosted GitLab instance, initialize the update checker like this:
	```php
	use YahnisElsts\PluginUpdateChecker\v5p0\Vcs\PluginUpdateChecker;
	use YahnisElsts\PluginUpdateChecker\v5p0\Vcs\GitLabApi;
	
	$myUpdateChecker = new PluginUpdateChecker(
		new GitLabApi('https://myserver.com/user-name/repo-name/'),
		__FILE__,
		'unique-plugin-or-theme-slug'
	);
	//Optional: Add setAuthentication(...) and setBranch(...) as shown above.  
	```
	If you're using a self-hosted GitLab instance and [subgroups or nested groups](https://docs.gitlab.com/ce/user/group/subgroups/index.html), you have to tell the update checker which parts of the URL are subgroups:
	```php
	use YahnisElsts\PluginUpdateChecker\v5p0\Vcs\PluginUpdateChecker;
	use YahnisElsts\PluginUpdateChecker\v5p0\Vcs\GitLabApi;
   
	$myUpdateChecker = new PluginUpdateChecker(
		new GitLabApi(
			'https://myserver.com/group-name/subgroup-level1/subgroup-level2/subgroup-level3/repo-name/', 
			null, 
			'subgroup-level1/subgroup-level2/subgroup-level3'
		),
		__FILE__,
		'unique-plugin-or-theme-slug'
	);
	```

3. Plugins only: Add a `readme.txt` file formatted according to the [WordPress.org plugin readme standard](https://wordpress.org/plugins/readme.txt) to your repository. The contents of this file will be shown when the user clicks the "View version 1.2.3 details" link.

#### How to Release a GitLab Update
A Gitlab repository can be checked for updates in 4 different ways.

1. **Stable branch** (other than `master` or `main`):
	- Point the update checker at any stable, production-ready branch and PUC will periodically check the `Version` header in the main plugin file or `style.css` and display a notification if it's greater than the installed version.
	- Add the following code:
		```php
		//Add the following code to your main plugin file or `functions.php` file to check for updates from a custom branch
		$myUpdateChecker->setBranch('stable-branch-name');
		```
	- Caveats:
		- If you set the branch to `main` (the default) or `master` (the historical default), the update checker will look for recent releases and tags first. It'll only use the `main` or `master` branch if it doesn't find anything else suitable.

2. **GitLab Releases using Generic Packages**:
	- Use a Gitlab CI/CD Pipeline to automatically generate your update on release using a Generic Package. The benefit of using Generic Package assets over the Source Code assets is that the code can already be built and production ready.
	- Add the following code:
		```php
		//Add the following code to your main plugin file or `functions.php` file to check for a new update from releases using generic packages
		$myUpdateChecker->getVcsApi()->enableReleasePackages();
		```
	- PUC will periodically check the release version (i.e. the tag name of the release) and will display a notification if the release is a greater version than the installed version.
	- The release tag name should loosely follow [SemVer](https://semver.org/) but these are all valid release names: `v1.2.3`, `v1.2-foo`, `1.2.3_rc1-ABC`, `1.2.3.4.5` However, be warned that it's not smart enough to filter out alpha/beta/RC versions. If that's a problem, you might want to use GitLab branches instead.
	- For more information about *Gitlab Release Generic Packages* refer to the following links:
		- [Gitlab CI/CD Release Documentation](https://docs.gitlab.com/ee/user/project/releases/#create-release-from-gitlab-ci)
		- [Gitlab Release Assets as Generic Package Documentation](https://gitlab.com/gitlab-org/release-cli/-/tree/master/docs/examples/release-assets-as-generic-package/)
		- [Example .gitlab-ci.yml file using Release Generic Packages for generating a update package from the Sensei-LMS wordpress plugin](https://gist.github.com/timwiel/9dfd3526c768efad4973254085e065ce)


3. **GitLab Releases using Source Code Assets**:
    - Create a new release using the "Releases" feature on Gitlab.
    - Add the following code:
        ```php
        //Add the following code to your main plugin file or `functions.php` file to check for a new update from releases using release assets
        $myUpdateChecker->getVcsApi()->enableReleaseAssets();
        ```
    - PUC will periodically check the release version (based on release tag name) and display a notification if the release version is greater than the installed version.
    - The release name should loosely follow [SemVer](https://semver.org/) but these are all valid release names: `v1.2.3`, `v1.2-foo`, `1.2.3_rc1-ABC`, `1.2.3.4.5` However, be warned that it's not smart enough to filter out alpha/beta/RC versions. If that's a problem, you might want to use GitLab branches instead.


4. **Tags** (this is the default option):
	- To release version 1.2.3, create a new Git tag named `v1.2.3` or `1.2.3`.
	- Optionally, add the following code:
		```php
		//Add the following code to your main plugin file or `functions.php` file to check for updates from the default branch
		$myUpdateChecker->setBranch('master'); //or 'main'
		```
	- PUC doesn't require strict adherence to [SemVer](https://semver.org/). These are all valid tag names: `v1.2.3`, `v1.2-foo`, `1.2.3_rc1-ABC`, `1.2.3.4.5`. However, be warned that it's not smart enough to filter out alpha/beta/RC versions. If that's a problem, you might want to use GitLab branches instead.

Migrating from 4.x
------------------

Older versions of the library didn't use namespaces. Code that was written for those versions will need to be updated to work with the current version. At a minimum, you'll need to change the factory class name. 

Old code:
```php
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://example.com/info.json',
	__FILE__,
	'my-slug'
);
```

New code:
```php
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://example.com/info.json',
	__FILE__,
	'my-slug'
);
```

Other classes have also been renamed, usually by simply removing the `Puc_vXpY_` prefix and converting `Category_Thing` to `Category\Thing`. Here's a table of the most commonly used classes and their new names:

| Old class name                      | New class name                                                 |
|-------------------------------------|----------------------------------------------------------------|
| `Puc_v4_Factory`                    | `YahnisElsts\PluginUpdateChecker\v5\PucFactory`                |
| `Puc_v4p13_Factory`                 | `YahnisElsts\PluginUpdateChecker\v5p0\PucFactory`              |
| `Puc_v4p13_Plugin_UpdateChecker`    | `YahnisElsts\PluginUpdateChecker\v5p0\Plugin\UpdateChecker`    |
| `Puc_v4p13_Theme_UpdateChecker`     | `YahnisElsts\PluginUpdateChecker\v5p0\Theme\UpdateChecker`     |
| `Puc_v4p13_Vcs_PluginUpdateChecker` | `YahnisElsts\PluginUpdateChecker\v5p0\Vcs\PluginUpdateChecker` |
| `Puc_v4p13_Vcs_ThemeUpdateChecker`  | `YahnisElsts\PluginUpdateChecker\v5p0\Vcs\ThemeUpdateChecker`  |
| `Puc_v4p13_Vcs_GitHubApi`           | `YahnisElsts\PluginUpdateChecker\v5p0\Vcs\GitHubApi`           |
| `Puc_v4p13_Vcs_GitLabApi`           | `YahnisElsts\PluginUpdateChecker\v5p0\Vcs\GitLabApi`           |
| `Puc_v4p13_Vcs_BitBucketApi`        | `YahnisElsts\PluginUpdateChecker\v5p0\Vcs\BitBucketApi`        |

License Management
------------------

Currently, the update checker doesn't have any built-in license management features. It only provides some hooks that you can use to, for example, append license keys to update requests (`$updateChecker->addQueryArgFilter()`). If you're looking for ways to manage and verify licenses, please post your feedback in [this issue](https://github.com/YahnisElsts/plugin-update-checker/issues/222).

Resources
---------

- [This blog post](https://w-shadow.com/blog/2010/09/02/automatic-updates-for-any-plugin/) has more information about the update checker API. *Slightly out of date.*
- [Debug Bar](https://wordpress.org/plugins/debug-bar/) - useful for testing and debugging the update checker.
- [Update format reference](https://docs.google.com/spreadsheets/d/1eOBbW7Go2qEQXReOOCdidMTf_tDYRq4JfegcO1CBPIs/edit?usp=sharing) - describes all fields supported by the JSON-based update information format used by the update checker. Only covers plugins. Themes use a similar but more limited format.
- [Securing download links](https://w-shadow.com/blog/2013/03/19/plugin-updates-securing-download-links/) - a general overview.
- [A GUI for entering download credentials](https://open-tools.net/documentation/tutorial-automatic-updates.html#wordpress)
- [Theme Update Checker](https://w-shadow.com/blog/2011/06/02/automatic-updates-for-commercial-themes/) - an older, theme-only variant of this update checker.
