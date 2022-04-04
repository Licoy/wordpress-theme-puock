# 更好的中文文案排版
 
统一中文文案、排版的相关用法，降低团队成员之间的沟通成本，增强网站气质。

Web 排版强迫症患者的福音。

[![Build Status](https://travis-ci.org/jxlwqq/chinese-typesetting.svg?branch=master)](https://travis-ci.org/jxlwqq/chinese-typesetting)
[![StyleCI](https://github.styleci.io/repos/142371176/shield?branch=master)](https://github.styleci.io/repos/142371176)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jxlwqq/chinese-typesetting/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jxlwqq/chinese-typesetting/?branch=master)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fjxlwqq%2Fchinese-typesetting.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fjxlwqq%2Fchinese-typesetting?ref=badge_shield)

## 安装
使用 [Composer](https://getcomposer.org/) 安装：
```bash
composer require "jxlwqq/chinese-typesetting"
```

## 使用

### 添加空格

```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

$text = '今天，我在Apple Store上购买了一台13英寸MacBook Pro笔记本电脑，花费了14188元。';
$chineseTypesetting->insertSpace($text);
// output: 今天，我在 Apple Store 上购买了一台 13 英寸 MacBook Pro 笔记本电脑，花费了 14188 元。

$text = 'α碳原子为与有机物中与官能团相连的第一个碳原子，第二个为β碳原子，以此类推。';
$chineseTypesetting->insertSpace($text);
// output: α 碳原子为与有机物中与官能团相连的第一个碳原子，第二个为 β 碳原子，以此类推。
```

在中文与英文字母/用于数学、科学和工程的希腊字母/数字之间添加空格。

目前，比较主流的约定是在中文与英文之间添加空格。本包增加了对用于数学、科学和工程的希腊字母的支持。

### 清除空格
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

$text = '小林买了一部 iPhone X，他终于可以用上老婆的 iPhone 7 了 ，好开心！';
$chineseTypesetting->removeSpace($text);
// output: 小林买了一部 iPhone X，他终于可以用上老婆的 iPhone 7 了，好开心！
```
全角标点符号与其他字符之间无需添加空格。


### 全角转半角
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

$text = '这个名为 ＡＢＣ 的蛋糕只卖 １０００ 元。';
$chineseTypesetting->full2Half($text);
// output: 这个名为 ABC 的蛋糕只卖 1000 元。
```
有限度的全角转半角（英文、数字、空格以及一些特殊字符等使用半角字符）。


### 修复错误的标点符号
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

// 修复省略号的错误用法
$text = '她轻轻地哼起了《摇篮曲》：“月儿明，风儿静，树叶儿遮窗櫺啊…”';
$chineseTypesetting->fixPunctuation($text);
// output: 她轻轻地哼起了《摇篮曲》：“月儿明，风儿静，树叶儿遮窗櫺啊……”

// 中文后面使用全角中文标点
$text = '你好,世界.';
$chineseTypesetting->fixPunctuation($text);
// output: 你好，世界。

// 不重复使用中文标点符号
$text = '你好激动啊！！！';
$chineseTypesetting->fixPunctuation($text);
// output: 你好激动啊！
```

### 专有名词使用正确的大小写

```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

$text = '今天午休的时候，我突然回想起了电影《泰坦尼克号》中 rose 裸身让 jack 作画的情节。'
$chineseTypesetting->properNoun($text);
// output：今天午休的时候，我突然回想起了电影《泰坦尼克号》中 Rose 裸身让 Jack 作画的情节。

// 扩展词汇
$text = '今天中午，我在 kfc 边吃着汉堡，边用 iphone 欣赏着电影《泰坦尼克号》中 rose 裸身让 JACK 作画的情节。'
$chineseTypesetting->properNoun($text, ['iPhone']);
// output：今天中午，我在 KFC 边吃着汉堡，边用 iPhone 欣赏着电影《泰坦尼克号》中 Rose 裸身让 Jack 作画的情节。

// 忽略词汇
$text = 'siri 告诉我，玫瑰对应的英文单词是 rose。'
$chineseTypesetting->properNoun($text, [], ['Rose']);
// output：Siri 告诉我，玫瑰对应的英文单词是 rose。
```

英语专有名词的数据来自于 [Wiktionary](https://en.wiktionary.org/w/index.php?title=Category:English_proper_nouns)。[采集爬虫链接](https://github.com/jxlwqq/english-proper-nouns)

Wiktionary 声明收录了 61765 条英语专用名词，实际爬取量为 61711 条。并使用以下原则进行数据的整理和筛选：

* 使用 `is_numeric()` 方法，剔除诸如 `007`、 等词汇；
* 使用 `'/\W/'` 正则，剔除诸如 `ǃXóõ` 等词汇；
* 剔除 `strlen` 方法，剔除 `A` 等单字节字符词汇；
* 剔除跟 HTML、CSS、JavaScript 保留字冲突的词汇。


### 清除 HTML 标签的样式
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

// 清除 Class 属性
$text = '<p class="class-name">你好，世界。</p>';
$chineseTypesetting->removeClass($text);
// output: <p>你好，世界。</p>

// 清除 ID 属性
$text = '<p id="id-name">你好，世界。</p>';
$chineseTypesetting->removeId($text);
// output: <p>你好，世界。</p>

// 清除 Style 属性
$text = '<p style="color: #FFFFFF;">你好，世界。</p>';
$chineseTypesetting->removeStyle($text);
// output: <p>你好，世界。</p>
```

### 清除空的段落标签
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

// 清除空的段落标签
$text = '<p>你好，世界。</p><p></p>';
$chineseTypesetting->removeEmptyParagraph($text);
// output: <p>你好，世界。</p>
```

### 清除所有空的标签
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

// 清除所有空的标签
$text = '<p>你好，世界。<span></span></p>';
$chineseTypesetting->removeEmptyTag($text);
// output: <p>你好，世界。</p>
```

### 清除段首缩进
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

// 清除段首缩进
$text = '<p>  你好，世界。</p>';
$chineseTypesetting->removeIndent($text);
// output: <p>你好，世界。</p>
```

首行是否缩进，[争议较大](https://zh.wikipedia.org/wiki/Wikipedia:投票/段落空兩格)，个人倾向于段首空两格，会破坏美感的观点。

### 使用指定的多种方法来纠正排版
```php
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

$chineseTypesetting = new ChineseTypesetting();

// 使用指定方法来纠正排版（推荐此用法）
$text = '<p class="class-name" style="color: #FFFFFF;"> Hello世界。</p>';
$chineseTypesetting->correct($text, ['insertSpace', 'removeClass', 'removeIndent']);
// output: <p style="color: #FFFFFF;">Hello 世界。</p>

// 使用全部方法来纠正排版（不推荐此用法）
$text = '<p class="class-name" style="color: #FFFFFF;"> Hello世界。</p>';
$chineseTypesetting->correct($text);
// output: <p>Hello 世界。</p>
```

## 参考

* [中文文案排版指北](https://github.com/sparanoid/chinese-copywriting-guidelines)
* [中文技术文档的写作规范](https://github.com/ruanyf/document-style-guide)
* [用于数学、科学和工程的希腊字母](https://zh.wikipedia.org/wiki/用於數學、科學和工程的希臘字母)
* [English proper nouns](https://en.wiktionary.org/w/index.php?title=Category:English_proper_nouns)


## Change Log

* 1.2.0：新增 `properNoun()` 方法，用于纠正专有名词的大小写；
* 1.2.1：`removeEmptyTag()` 和 `removeEmptyParagraph()` 方法支持清除嵌套空标签；
* 1.2.2：新增 `removeSpace()` 方法，清除全角标点符号与其他字符之间的空格；

## License
`chinese-typesetting` is licensed under [The MIT License (MIT)](./LICENSE).
