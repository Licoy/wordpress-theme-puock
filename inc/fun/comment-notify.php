<?php

function get_comment_notify_template($comment,$parent_id)
{
    $blog_name = get_option('blogname');
    $article_title = get_the_title($comment->comment_post_ID);
    $comment_link = get_comment_link($parent_id,array("type" => "all"));
    $parent_comment_content = trim(get_comment($parent_id)->comment_content);
    $reply_author = $comment->comment_author;
    $reply_content = trim($comment->comment_content);
    
    $header_text = sprintf(
        /* translators: 1: Blog name, 2: Link to article, 3: Article title */
        __('您在%1$s的<a href="%2$s" target="_blank">《%3$s》</a>文章中的评论有了新的回复：', PUOCK),
        $blog_name,
        $comment_link,
        $article_title
    );
    
    $your_comment_text = __('你的评论内容为：', PUOCK);
    $reply_text = sprintf(
        /* translators: %s: Author name of the reply */
        __('您收到"%s"对您的回复为：', PUOCK),
        $reply_author
    );
    $view_article_text = sprintf(
        /* translators: %s: Link to the comment */
        __('您也可以<a target="_blank" href="%s">直接点我进入原文章</a>以查看评论~', PUOCK),
        $comment_link
    );
    $footer_text = __('此邮件由系统发出，请勿直接回复，谢谢合作！', PUOCK);
    
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
            {$header_text}
        </div>
        <div class=\"main\">
            {$your_comment_text}
            <div class=\"content-item me\">
                {$parent_comment_content}
            </div>
            {$reply_text}
            <div class=\"content-item\">
                {$reply_content}
            </div>
            <div class=\"tips\">
                {$view_article_text}
            </div>
        </div>
        <div class=\"footer\">
            <span>{$footer_text}</span>
        </div>
    </div>";
    return $res;
}