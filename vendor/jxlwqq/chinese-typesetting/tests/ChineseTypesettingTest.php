<?php
/**
 * Created by PhpStorm.
 * User: jxlwqq
 * Date: 2018/8/20
 * Time: 13:55.
 */
use Jxlwqq\ChineseTypesetting\ChineseTypesetting;

class ChineseTypesettingTest extends \PHPUnit\Framework\TestCase
{
    public function testRemoveId()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p id="id-name">你好，世界。</p>';
        $this->assertEquals('<p>你好，世界。</p>', $chineseTypesetting->removeId($text));
    }

    public function testRemoveEmptyParagraph()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p>你好，世界。</p><p></p>';
        $this->assertEquals('<p>你好，世界。</p>', $chineseTypesetting->removeEmptyParagraph($text));
    }

    public function testRemoveEmptyTag()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p>你好，世界。<span></span></p>';
        $this->assertEquals('<p>你好，世界。</p>', $chineseTypesetting->removeEmptyTag($text));
    }

    public function testFixPunctuation()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '她轻轻地哼起了《摇篮曲》:“月儿明,风儿静，树叶儿遮窗櫺啊…”';
        $this->assertEquals('她轻轻地哼起了《摇篮曲》：“月儿明，风儿静，树叶儿遮窗櫺啊……”', $chineseTypesetting->fixPunctuation($text));
    }

    public function testInsertSpace()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '今天，我在Apple Store上购买了一台13英寸MacBook Pro笔记本电脑，花费了14188元。';
        $this->assertEquals('今天，我在 Apple Store 上购买了一台 13 英寸 MacBook Pro 笔记本电脑，花费了 14188 元。', $chineseTypesetting->insertSpace($text));
    }

    public function testRemoveSpace()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '小林买了一部 iPhone X，他终于可以用上老婆的 iPhone 7 了 ，好开心！';
        $this->assertEquals('小林买了一部 iPhone X，他终于可以用上老婆的 iPhone 7 了，好开心！', $chineseTypesetting->removeSpace($text));
    }

    public function testRemoveClass()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p class="class-name">你好，世界。</p>';
        $this->assertEquals('<p>你好，世界。</p>', $chineseTypesetting->removeClass($text));
    }

    public function testRemoveIndent()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p> 你好，世界。</p>';
        $this->assertEquals('<p>你好，世界。</p>', $chineseTypesetting->removeIndent($text));
    }

    public function testFull2Half()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '这个名为 ＡＢＣ 的蛋糕只卖 １０００ 元。';
        $this->assertEquals('这个名为 ABC 的蛋糕只卖 1000 元。', $chineseTypesetting->full2Half($text));
    }

    public function testRemoveStyle()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p style="color: #FFFFFF;">你好，世界。</p>';
        $this->assertEquals('<p>你好，世界。</p>', $chineseTypesetting->removeStyle($text));
    }

    public function testCorrect()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '<p class="class-name" style="color: #FFFFFF;"> Hello世界.</p>';
        $this->assertEquals('<p>Hello 世界。</p>', $chineseTypesetting->correct($text));
    }

    public function testProperNoun()
    {
        $chineseTypesetting = new ChineseTypesetting();
        $text = '今天午休的时候，我突然回想起了电影《泰坦尼克号》中 rose 裸身让 jack 作画的情节。';
        $this->assertEquals('今天午休的时候，我突然回想起了电影《泰坦尼克号》中 Rose 裸身让 Jack 作画的情节。', $chineseTypesetting->properNoun($text));
    }
}
