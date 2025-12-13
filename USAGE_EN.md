# Puock Theme Feature Usage Guide

This document provides detailed instructions on how to use common features in the Puock theme, including Friendship Links and Alert Boxes.

## Table of Contents

- [Friendship Links Feature](#friendship-links-feature)
  - [Enable Link Manager](#enable-link-manager)
  - [Add Friendship Links](#add-friendship-links)
  - [Create Links Page](#create-links-page)
  - [Display Links on Homepage](#display-links-on-homepage)
- [Puock Alert Box Feature](#puock-alert-box-feature)
  - [Basic Usage](#basic-usage)
  - [Available Types](#available-types)
  - [Adding Icons](#adding-icons)
  - [Outline Style](#outline-style)
- [Other Shortcode Features](#other-shortcode-features)
  - [Reply to View](#reply-to-view)
  - [Login to View](#login-to-view)
  - [Password Protected](#password-protected)
  - [Collapse Panel](#collapse-panel)
  - [File Download](#file-download)
  - [Video Player](#video-player)
  - [Audio Player](#audio-player)
  - [GitHub Card](#github-card)

---

## Friendship Links Feature

The Friendship Links feature is based on WordPress's built-in Link Manager and helps you manage and display website links.

### Enable Link Manager

WordPress hides the Link Manager by default in newer versions. If you don't see the "Links" option in the WordPress admin menu, you need to enable it first:

**Method 1: Using a Plugin (Recommended)**
1. Go to `Plugins` → `Add New` in WordPress admin
2. Search for `Link Manager`
3. Install and activate the plugin
4. After refreshing, the `Links` menu will appear in the left sidebar

**Method 2: Adding Code**
Add the following code to your theme's `functions.php` file or using a code snippet plugin:

```php
add_filter('pre_option_link_manager_enabled', '__return_true');
```

### Add Friendship Links

1. Click `Links` → `Add New` in the WordPress admin sidebar
2. Fill in the link information:
   - **Name**: Name of the website
   - **Web Address**: URL of the website (required)
   - **Description**: Brief description of the website (optional)
   - **Categories**: Select or create categories for organizing links
3. Advanced options:
   - **Link Image**: Set the website's logo or icon URL
   - **Link Target**: Usually select `_blank` (open in new window)
   - **Link Relationship (rel)**: Can be set to `nofollow` or `friend`
4. Click `Add Link` to save

**Managing Link Categories:**
- Click `Links` → `Link Categories` to create and manage categories
- Recommended categories: "Featured Links", "Partners", "Personal Blogs", etc.

### Create Links Page

1. Go to `Pages` → `Add New` in WordPress admin
2. Enter a page title, such as "Friendship Links"
3. In the right sidebar under `Page Attributes` → `Template`, select **Friendship Links (`友情链接`)** template
4. You can add introductory text in the page editor (optional)
5. In the custom fields or page settings area at the bottom:
   - **page_links_id**: Select the link category ID(s) to display
   - **use_theme_link_forward**: Whether to use theme's redirect feature (for statistics and protection)
6. Click `Publish`

**How to Get Link Category ID:**
1. Go to `Links` → `Link Categories`
2. Hover over a category name
3. Look at the browser's status bar or check the URL for `tag_ID=123`
4. Note this ID number for use in page settings

### Display Links on Homepage

To display a friendship links module on the homepage or other locations:

1. Go to Puock theme settings
2. Find `Homepage Settings` or `Global Settings`
3. Look for `Friendship Links` related settings:
   - **index_link_id**: Select link category ID to display on homepage
   - **index_link_order**: Sort order (ASC ascending / DESC descending)
   - **index_link_order_by**: Sort by (link_id / link_name / link_rating, etc.)
   - **link_page**: Specify the full links page ID (displays "More Links" button)
4. Save settings

---

## Puock Alert Box Feature

Puock theme provides various styled alert box shortcodes for use in posts or pages.

### Basic Usage

Use the following format in the post editor:

```
[t-type]Your alert message here[/t-type]
```

**Example:**

```
[t-primary]This is a primary alert box[/t-primary]
```

Result: Displays an alert box with blue background

### Available Types

Puock theme supports 6 types of alert boxes:

| Shortcode | Type | Color | Use Case |
|-----------|------|-------|----------|
| `[t-primary]` | Primary | Blue | General information |
| `[t-success]` | Success | Green | Success messages, positive info |
| `[t-info]` | Info | Light Blue | Supplementary info, tips |
| `[t-warning]` | Warning | Yellow | Warnings, cautions |
| `[t-danger]` | Danger | Red | Danger warnings, errors |
| `[t-dark]` | Dark | Dark Gray | Secondary info, quotes |

**Usage Examples:**

```
[t-success]Success! Your settings have been saved.[/t-success]

[t-warning]Warning: This action cannot be undone. Proceed with caution.[/t-warning]

[t-danger]Error: File upload failed. Please check file size and format.[/t-danger]

[t-info]Tip: You can modify default options in settings.[/t-info]
```

### Adding Icons

You can add Font Awesome icons to alert boxes:

```
[t-primary icon="fa fa-info-circle"]Alert box with icon[/t-primary]
```

**Common Icon Examples:**

```
[t-success icon="fa fa-check-circle"]Task completed successfully[/t-success]

[t-warning icon="fa fa-exclamation-triangle"]Please note the operation risk[/t-warning]

[t-danger icon="fa fa-times-circle"]A serious error occurred[/t-danger]

[t-info icon="fa fa-lightbulb"]Here's a helpful tip[/t-info]
```

Find more icons at [Font Awesome website](https://fontawesome.com/icons).

### Outline Style

Use the `outline="true"` parameter to enable outline style (border style without background fill):

```
[t-primary outline="true"]This is an outline style alert box[/t-primary]
```

**Complete Example (with icon and outline):**

```
[t-info outline="true" icon="fa fa-star"]This is an outline alert box with star icon[/t-info]
```

---

## Other Shortcode Features

Puock theme provides many other useful shortcode features:

### Reply to View

Hide content that requires readers to comment before viewing:

```
[reply]
This content requires a comment to view
Supports multiple lines
[/reply]
```

### Login to View

Content visible only to logged-in users:

```
[login]
This content is visible only to logged-in users
[/login]
```

### Login and Email Verified to View

Content visible only to logged-in users with verified email:

```
[login_email]
This content requires login and email verification to view
[/login_email]
```

### Password Protected

Protect content with a password:

```
[password pass="123456" desc="Please enter password to view hidden content"]
This content requires a password to view
[/password]
```

Parameters:
- `pass`: Set password (required)
- `desc`: Prompt text (optional, default: "Enter password to view hidden content")

### Collapse Panel

Create collapsible/expandable content:

```
[collapse title="Click to expand and view detailed content"]
This is the collapsed content
Can be long text
[/collapse]
```

### File Download

Display file download information box:

```
[download file="Software-v1.0.zip" size="25.6MB"]
https://example.com/download/file.zip
[/download]
```

Parameters:
- `file`: File name
- `size`: File size

### Video Player

Embed video player:

```
[video url="https://example.com/video.mp4" autoplay="false" pic="https://example.com/poster.jpg"]
```

Parameters:
- `url`: Video URL (required)
- `autoplay`: Auto-play (true/false, default false)
- `type`: Video type (auto/hls/flv, etc.)
- `pic`: Video poster image
- `ssl`: Force HTTPS (true/false)

### Audio Player

Embed audio player:

```
[music]https://example.com/audio.mp3[/music]
```

### GitHub Card

Display GitHub repository information card:

```
[github]username/repository[/github]
```

**Example:**

```
[github]Licoy/wordpress-theme-puock[/github]
```

---

## Usage Tips

### 1. Using Shortcodes in Gutenberg Editor

- Add a `Shortcode` block
- Enter the shortcode in the block
- Preview or publish to see the effect

### 2. Using Shortcodes in Classic Editor

- Switch to `Text` mode (not `Visual` mode)
- Enter the shortcode directly
- Switch back to `Visual` mode to preview

### 3. Using Puock Alert Block (Gutenberg)

If using Gutenberg editor, you can directly add the `Puock Alert` block:
1. Click `+` to add a block
2. Search for `Puock` or `Alert`
3. Select Puock Alert (Puock提示框) block
4. Configure color, text, icons, etc. in block settings

### 4. Nested Shortcodes

Shortcodes support nesting:

```
[collapse title="View more information"]
[t-info]This is an alert box inside a collapse panel[/t-info]
Some text content here
[t-warning]This is a warning alert[/t-warning]
[/collapse]
```

### 5. Custom Styles

Add custom CSS classes to alert boxes:

```
[t-primary class="my-custom-class"]Alert box with custom style[/t-primary]
```

Then add styles in theme's custom CSS:

```css
.my-custom-class {
    font-size: 18px;
    border-radius: 10px;
}
```

---

## FAQ

### Q1: Why don't I see the "Links" menu?

A: WordPress hides the Link Manager by default in newer versions. You need to install the Link Manager plugin or add code to enable it. See [Enable Link Manager](#enable-link-manager).

### Q2: Friendship links page displays blank?

A: Check the following:
1. Have you added friendship links and set them as visible?
2. Is the page template set to Friendship Links (友情链接)?
3. Is the `page_links_id` field correctly set with category ID?
4. Is the link category ID correct?

### Q3: Shortcode not working, displays as plain text?

A: Possible reasons:
1. Incorrect shortcode format (check brackets and tag names)
2. Using shortcode inside a code block (shortcodes aren't parsed in code blocks)
3. Theme or plugin conflict (try switching to default theme for testing)

### Q4: Alert box style incorrect or not displaying?

A: Check:
1. Is the theme version up to date?
2. Is browser cache cleared?
3. Is the shortcode type name correct (e.g., `t-primary` not `t-blue`)?

### Q5: How to get link category ID?

A: Go to `Links` → `Link Categories`, hover over a category name, and look at the browser's status bar or URL for `tag_ID=number`. This number is the category ID.

---

## More Help

- Theme Documentation: https://www.licoy.cn/puock-doc.html
- Issue Feedback: https://github.com/Licoy/wordpress-theme-puock/issues
- Community Group: https://licoy.cn/go/puock-update.php?r=qq_qun

---

**Documentation Version**: 1.0  
**Last Updated**: 2024
