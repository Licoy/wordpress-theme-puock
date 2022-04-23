<?php
/**
 * @author jxlwqq
 * @link https://github.com/jxlwqq/chinese-typesetting
 * @license MIT
 */

class ChineseTypesetting
{
    /**
     * cjk 是中日韩统一表意文字缩写
     * cjk is short for Chinese, Japanese and Korean.
     *
     * @link http://unicode-table.com/en/
     *
     * @var string
     */
    private $cjk = ''.
    '\x{2e80}-\x{2eff}'.
    '\x{2f00}-\x{2fdf}'.
    '\x{3040}-\x{309f}'.
    '\x{30a0}-\x{30ff}'.
    '\x{3100}-\x{312f}'.
    '\x{3200}-\x{32ff}'.
    '\x{3400}-\x{4dbf}'.
    '\x{4e00}-\x{9fff}'.
    '\x{f900}-\x{faff}';

    /**
     * ln 是英文字母、希腊字母（用于数学、科学与工程）和阿拉伯数字的缩写
     * ln is short of alphabetical letters, greek letters and numerical digits and symbols.
     *
     * @link https://en.wikipedia.org/wiki/Greek_letters_used_in_mathematics,_science,_and_engineering
     *
     * @var string
     */
    private $ln = ''.
    'A-Za-z'.
    'Α-Ωα-ω'.
    '0-9';

    /**
     * 保留的全角标点符号.
     *
     * @var string
     */
    private $fullwidthPunctuation = '！？。，；：、“”‘’『』「」〖〗【】《》（）';

    /**
     * 空格
     *
     * @var string
     */
    private $space = '\s|&nbsp;|　';

    /**
     * 使用全部或指定的方法来纠正排版
     * Correct typesetting error.
     *
     * @param string $text
     * @param array  $methods
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public function correct($text, array $methods = [])
    {
        if (empty($methods)) {
            $class = new \ReflectionClass($this);
            $methodsList = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methodsList as $methodObj) {
                $methods[] = $methodObj->name;
            }
        }

        $methods = array_unique($methods);

        // removeEmptyTag 方法包含了 removeEmptyParagraph 方法的功能，如果这两个函数都存在，则需去除 removeEmptyParagraph 方法
        if (in_array('removeEmptyTag', $methods)) {
            $methods = array_diff($methods, ['removeEmptyParagraph']);
        }

        // insertSpace 方法应该是数组的最后一个元素
        // the method insertSpace should be the end of array
        if (in_array('insertSpace', $methods)) {
            $methods = array_diff($methods, ['insertSpace']);
            array_push($methods, 'insertSpace');
        }

        foreach ($methods as $method) {
            if (__FUNCTION__ == $method || !method_exists($this, $method)) {
                continue;
            }
            $text = $this->$method($text);
        }

        return $text;
    }

    /**
     * 修复错误的标点符号
     * Fix incorrect punctuations.
     *
     * update base on @link https://github.com/ricoa/copywriting-correct/blob/master/src/Correctors/CharacterCorrector.php
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function fixPunctuation($text)
    {
        // 正确使用省略号
        $text = preg_replace('/([。\.]){3,}|(…){1}/iu', '……', $text);
        $text = preg_replace('/(……){2,}/iu', '……', $text);

        // 中文以及中文标点符号）》后使用全角中文标点符号（包括！？。，（）：；）
        $text = preg_replace_callback('/(['.$this->cjk.'）》”])([!?\.,\(\):;])/iu', function ($matches) {
            $replace = [
                '!' => '！',
                '?' => '？',
                '.' => '。',
                ',' => '，',
                '(' => '（',
                ')' => '）',
                ':' => '：',
                ';' => '；',
            ];

            return $matches[1].$replace[$matches[2]];
        }, $text);

        // 不重复使用中文标点符号，重复时只保留第一个
        $text = preg_replace('/(['.$this->fullwidthPunctuation.'])\1{1,}/iu', '\1', $text);

        return $text;
    }

    /**
     * 有限度的全角转半角（英文、数字、空格以及某些特殊字符等使用半角字符）
     * Limited full-width to half-width transformer.
     *
     * @link https://github.com/mzlogin/chinese-copywriting-guidelines#全角和半角
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function full2Half($text)
    {
        $arr = ['０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５'     => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ'     => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ'     => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ'     => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ'     => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ'     => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ'     => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ'     => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ'     => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ'     => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ'     => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ'     => 'y', 'ｚ' => 'z',
            '－'     => '-', '　' => ' ', '／' => '/',
            '％'     => '%', '＃' => '#', '＠' => '@', '＆' => '&', '＜' => '<',
            '＞'     => '>', '［' => '[', '］' => ']', '｛' => '{', '｝' => '}',
            '＼'     => '\\', '｜' => '|', '＋' => '+', '＝' => '=', '＿' => '_',
            '＾'     => '^', '￣' => '~', '｀' => '`', ];

        return strtr($text, $arr);
    }

    /**
     * 在中文与英文字母/用于数学、科学和工程的希腊字母/数字之间添加空格
     * Insert a space between Chinese character and English/Greek/Number character.
     *
     * update base on @link https://github.com/Rakume/pangu.php/blob/master/pangu.php
     *
     * @link https://github.com/mzlogin/chinese-copywriting-guidelines#空格
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function insertSpace($text)
    {
        $patterns = [
            'cjk_quote' => [
                '(['.$this->cjk.'])(["\'])',
                '$1 $2',
            ],
            'quote_cjk' => [
                '(["\'])(['.$this->cjk.'])',
                '$1 $2',
            ],
            'fix_quote' => [
                '(["\']+)(\s*)(.+?)(\s*)(["\']+)',
                '$1$3$5',
            ],
            'cjk_hash' => [
                '(['.$this->cjk.'])(#(\S+))',
                '$1 $2',
            ],
            'hash_cjk' => [
                '((\S+)#)(['.$this->cjk.'])',
                '$1 $3',
            ],
            'cjk_operator_ans' => [
                '(['.$this->cjk.'])(['.$this->ln.'])([\+\-\*\/=&\\|<>])',
                '$1 $2 $3',
            ],
            'ans_operator_cjk' => [
                '([\+\-\*\/=&\\|<>])(['.$this->ln.'])(['.$this->cjk.'])',
                '$1 $2 $3',
            ],
            'bracket' => [
                [
                    '(['.$this->cjk.'])([<\[\{\(]+(.*?)[>\]\}\)]+)(['.$this->cjk.'])',
                    '$1 $2 $4',
                ],
                [
                    'cjk_bracket' => [
                        '(['.$this->cjk.'])([<>\[\]\{\}\(\)])',
                        '$1 $2',
                    ],
                    'bracket_cjk' => [
                        '([<>\[\]\{\}\(\)])(['.$this->cjk.'])',
                        '$1 $2',
                    ],
                ],
            ],
            'fix_bracket' => [
                '([<\[\{\(]+)(\s*)(.+?)(\s*)([>\]\}\)]+)',
                '$1$3$5',
            ],
            'cjk_ans' => [
                '(['.$this->cjk.'])(['.$this->ln.'`@&%\=\$\^\*\-\+\\/|\\\])',
                '$1 $2',
            ],
            'ans_cjk' => [
                '(['.$this->ln.'`~!%&=;\|\,\.\:\?\$\^\*\-\+\/\\\])(['.$this->cjk.'])',
                '$1 $2',
            ],
        ];
        foreach ($patterns as $key => $value) {
            if ($key === 'bracket') {
                $old = $text;
                $new = preg_replace('/'.$value[0][0].'/iu', $value[0][1], $text);
                $text = $new;
                if ($old === $new) {
                    foreach ($value[1] as $val) {
                        $text = preg_replace('/'.$val[0].'/iu', $val[1], $text);
                    }
                }
                continue;
            }
            $text = preg_replace('/'.$value[0].'/iu', $value[1], $text);
        }

        return $text;
    }

    /**
     * 全角标点符号与其他字符之间无需添加空格；.
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function removeSpace($text)
    {
        $patterns = [
            'fullwidth_space' => [
                '(['.$this->fullwidthPunctuation.'])(\s)+',
                '$1',
            ],
            'space_fullwidth' => [
                '(\s)+(['.$this->fullwidthPunctuation.'])',
                '$2',
            ],
        ];

        foreach ($patterns as $key => $value) {
            $text = preg_replace('/'.$value[0].'/u', $value[1], $text);
        }

        return $text;
    }

    /**
     * 清除 Class 属性
     * Remove specific class of HTML tags.
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function removeClass($text)
    {
        return preg_replace('#\s(class)="[^"]+"#', '', $text);
    }

    /**
     * 清除 ID 属性
     * Remove specific id of HTML tags.
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function removeId($text)
    {
        return preg_replace('#\s(id)="[^"]+"#', '', $text);
    }

    /**
     * 清除 Style 属性
     * Remove specific style of HTML tags.
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function removeStyle($text)
    {
        return preg_replace('#\s(style)="[^"]+"#', '', $text);
    }

    /**
     * 清除空段落标签
     * Remove empty Paragraph tags.
     *
     * @param string $text
     * @param bool   $nested
     *
     * @return null|string|string[]
     */
    public function removeEmptyParagraph($text, $nested = true)
    {
        $pattern = '/<p[^>]*>(['.$this->space.']?)<\\/p[^>]*>/';
        if ($nested) {
            while (preg_match($pattern, $text)) {
                $text = preg_replace($pattern, '', $text);
            }
        } else {
            $text = preg_replace($pattern, '', $text);
        }

        return $text;
    }

    /**
     * 清除所有空标签
     * Remote all empty HTML tags.
     *
     * @param string $text
     * @param bool   $nested
     *
     * @return null|string|string[]
     */
    public function removeEmptyTag($text, $nested = true)
    {
        $pattern = '/<[^\/>]*>(['.$this->space.']?)*<\/[^>]*>/';
        if ($nested) {
            while (preg_match($pattern, $text)) {
                $text = preg_replace($pattern, '', $text);
            }
        } else {
            $text = preg_replace($pattern, '', $text);
        }

        return $text;
    }

    /**
     * 清除段首缩进.
     * Remove indent.
     *
     * @param string $text
     *
     * @return null|string|string[]
     */
    public function removeIndent($text)
    {
        return preg_replace('/<p([^>]*)>('.$this->space.')+/', '<p${1}>', $text);
    }
}
