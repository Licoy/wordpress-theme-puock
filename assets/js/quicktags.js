let shortCodeColors = {'primary':'主要','danger':'错误','warning':'警告','info':'信息','success':'成功','dark':'黑色'};
function genShortCodeForColor(title,colors,prefix){
    for (let color in shortCodeColors){
        let sc = prefix+color;
        if(title==="按钮"){
            QTags.addButton( sc, shortCodeColors[color]+title, "["+sc+" href='']", "[/"+sc+"]" );
        }else{
            QTags.addButton( sc, shortCodeColors[color]+title, "["+sc+"]", "[/"+sc+"]" );
        }
    }
}
QTags.addButton( 'h2', 'H2标签', "<h2>", "</h2>\n" );
QTags.addButton( 'h3', 'H3标签', "<h3>", "</h3>\n" );
QTags.addButton( 'zyy', '引用',  "<blockquote>", "</blockquote>\n" );
QTags.addButton( 'hr', '横线', "<hr />\n" );
QTags.addButton( 'hc', '回车', "<br />" );
QTags.addButton( 'jz', '居中', "<center>","</center>" );
QTags.addButton( 'nextpage', '换页', '<!--nextpage-->', "" );
QTags.addButton( 'collapse', '隐藏收缩', "[collapse title='']", '[/collapse]' );
genShortCodeForColor("提示框",shortCodeColors,'t-');
let btnShortCodeColors = shortCodeColors;
btnShortCodeColors['link'] = '链接';
genShortCodeForColor("按钮",btnShortCodeColors,'btn-');
let shortCodeIds = {
    'music':'音乐播放',
    'reply':'回复可见',
    'login':'登录可见',
};
for (let scId in shortCodeIds){
    QTags.addButton( scId, shortCodeIds[scId], "["+scId+"]", "[/"+scId+"]");
}
QTags.addButton( 'video', "视频播放", "[video url='' autoplay=false type='auto' pic='' class='']", "[/video]");
QTags.addButton( 'download', "文件下载", "[download file='' size='']", "[/download]");
QTags.addButton( 'password', "输入密码可见", "[password pass='']", "[/password]");