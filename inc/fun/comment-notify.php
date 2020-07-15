<?php

function get_comment_notify_template($comment,$parent_id)
{
    $res = "
    <style>
        #p-mail-notify{
            font-size: 14px;
            border:1px solid #dddddd;
            border-radius: 10px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        #p-mail-notify a{
            display: inline-block;
            text-decoration: none;
            transition: all .3s;
        }
        #p-mail-notify a:hover{
            padding:0 5px;
        }
        #p-mail-notify .header,#p-mail-notify .footer{
            background-color: #007bff;
            padding:15px;
            color:#fff;
        }
        #p-mail-notify .header{
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        #p-mail-notify .header a{
            color:#ffcc33;
        }
        #p-mail-notify .main .tips a{
            color:#007bff;
        }
        #p-mail-notify .footer{
            font-size: 12px;
            background-color: #8f969c;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        #p-mail-notify .main{
            padding:15px;
        }
        #p-mail-notify .main .content-item{
            padding:10px;
            background-color: #343a40;
            border-radius: 5px;
            color:#fff;
            margin:10px 0;
        }
        #p-mail-notify .main .tips{
            font-size: 12px;
            color:#343a40;
        }
    </style>
    <div id=\"p-mail-notify\">
        <div class=\"header\">
            您在".get_option('blogname')."的<a href=\"".get_comment_link($parent_id,array("type" => "all"))."\" target=\"_blank\">《".get_the_title($comment->comment_post_ID)."》</a>文章中的评论有了新的回复：
        </div>
        <div class=\"main\">
            你的评论内容为：
            <div class=\"content-item me\">
                ". trim(get_comment($parent_id)->comment_content) ."
            </div>
            您收到\"".$comment->comment_author."\"对您的回复为：
            <div class=\"content-item\">
                ". trim($comment->comment_content) ."
            </div>
            <div class=\"tips\">
                您也可以<a target=\"_blank\" href=\"".get_comment_link($parent_id,array("type" => "all"))."\">直接点我进入原文章</a>以查看评论~
            </div>
        </div>
        <div class=\"footer\">
            <span>此邮件由系统发出，请勿直接回复，谢谢合作！</span>
        </div>
    </div>";
    return $res;
}