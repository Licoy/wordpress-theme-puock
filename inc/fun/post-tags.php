<?php

function specs_get_first_char($s0){
    $char = ord($s0[0]);
    if($char >= ord("A") and $char <= ord("z") )return strtoupper($s0[0]);
    $s1 = iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return null;
}

function specs_pinyin($zh){
    $ret = "";
    $s1 = iconv("UTF-8","gb2312", $zh);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $zh){$zh = $s1;}
    $i = 0;
    $s1 = substr($zh,$i,1);
    $p = ord($s1);
    if($p > 160){
        $s2 = substr($zh,$i++,2);
        $ret .= specs_get_first_char($s2);
    }else{
        $ret .= $s1;
    }
    return strtoupper($ret);
}

function specs_show_tags() {
    if(!$out = get_option('specs_tags_list_cache')){
        $categories = get_terms( 'post_tag', array(
            'orderby'    => 'count',
            'hide_empty' => 1
        ));
        $r = array();
        foreach($categories as $v){
            for($i = 65; $i <= 90; $i++){
                if(specs_pinyin($v->name) == chr($i)){
                    $r[chr($i)][] = $v;
                }
            }
            for($i=48;$i<=57;$i++){
                if(specs_pinyin($v->name) == chr($i)){
                    $r[chr($i)][] = $v;
                }
            }
        }
        ksort($r);
        $out = "<ul class='pl-0' id='tags-main-index'>";
        for($i=65;$i<=90;$i++){
            $tagi = @$r[chr($i)];
            $out .= is_array($tagi) ? "<li class='a'><a href='#".chr($i)."'>".chr($i)."</a></li>" : "<li class='t'>".chr($i)."</li>";
        }
        for($i=48;$i<=57;$i++){
            $tagi = @$r[chr($i)];
            $out .= is_array($tagi) ? "<li class='a'><a href='#".chr($i)."'>".chr($i)."</a></li>" : "<li class='t'>".chr($i)."</li>";
        }
        $out .= "</ul><ul id='tags-main-box' class='pl-0'>";
        for($i=65;$i<=90;$i++){
            $tagi = @$r[chr($i)];
            if(is_array($tagi)){
                $out .= "<li class='n' id='".chr($i)."'><h4 class='tag-name'><span class='t-lg c-sub'>#</span>".chr($i)."</h4>";
                foreach($tagi as $tag){
                    $out .= "<a href='".get_tag_link($tag->term_id)."'>".$tag->name."(".$tag->count.")</a>";
                }
            }
        }
        for($i=48;$i<=57;$i++){
            $tagi = @$r[chr($i)];
            if(is_array($tagi)){
                $out .= "<li class='n' id='".chr($i)."'><h4 class='tag-name'><span class='t-lg c-sub'>#</span>".chr($i)."</h4>";
                foreach($tagi as $tag){
                    $out .= "<a href='".get_tag_link($tag->term_id)."'>".$tag->name."(".$tag->count.")</a>";
                }
            }
        }
        $out .= "</ul>";
        update_option('specs_tags_list_cache', $out);
    }
    echo $out;
}

//发布文章时清理标签缓存
function clear_post_tags_cache() {
    update_option('specs_tags_list_cache', '');
}
add_action('save_post', 'clear_post_tags_cache');