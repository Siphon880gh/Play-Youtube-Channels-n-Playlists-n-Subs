<?php

if( isset($_GET["channelName"]) && strlen($_GET["channelName"])>0 ) {
    $channelName = $_GET["channelName"];
    $channelLink = "https://www.youtube.com/user/$channelName/videos";

    $cmd1 = "node --no-warnings node-yt.js $channelName 2>&1";   
    $stdout1 = exec($cmd1, $res1);
    $link = "index.php?playlistId=$stdout1";

    // echo json_encode(["cmd1"=>$cmd1, "stdout1"=>$stdout1, "res1"=>$res1]);
    // echo "<a href='$link'>Link</a>";
    header("Location: $link");
    
}

?>