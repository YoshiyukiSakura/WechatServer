<?php
for ($i=0; $i < count($message); $i++) { 
    echo "时间:".$message[$i]['time']."\n";
    echo "内容:".$message[$i]['content']."<br>";
}