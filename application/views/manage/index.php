
<?php
echo "<h1><a href=\"http://kirisamenana.com/wechat/index.php/manage/message\" title=\"用户消息\">消息管理</a></h1>";
for ($i=0; $i < count($keywords); $i++) { 
    echo "关键字:".$keywords[$i]['keyword']."\n";
    echo "回复:".$keywords[$i]['content']."<br>";
}

