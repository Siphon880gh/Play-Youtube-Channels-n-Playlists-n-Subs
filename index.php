<?php
// Other defaults:
// Dua Lipa old music: OLAK5uy_n1RTVv-lJ6-4DjJmFUfF_MoRevOeYTM4Q
// Bebe Rexha old music: OLAK5uy_mc7x6vdCJWUpWXeF6NEY1ljjfwXoG18do
// RandomYt: PLJMMfLBtUHMlUCynG24upluI_pE9WFlII
$defaultPlaylistId = "OLAK5uy_lYY8Ips3wSdyDYyemlb_1xBvFCgLLd-YQ";
$playlistId = isset($_GET["playlistId"]) && strlen($_GET["playlistId"]) ? $_GET["playlistId"] : $defaultPlaylistId;

if( isset($_GET["playlistStart"]) && is_numeric(intval($_GET["playlistStart"])) ) {
    $num = intval($_GET["playlistStart"]);
    if($num<=1) $playlistStartIndex=-10; // Workaround API glitch: index 1 is consistently playing the third spot.negative number plays #1
    else $playlistStartIndex = $_GET["playlistStart"];
}
?>
<html>
<head>
    <title>Youtube Channel Shuffler by Weng</title>
    <meta property="fb:app_id" content="685633705547844" />
    <meta property="og:image" content="https://www.publicdomainpictures.net/pictures/250000/nahled/youtube-1515875370Xwr.jpg" />
    
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    
    <script>
    window.urlChange = {
        playlist: (arg1)=> {
            var params = new URLSearchParams(window.location.search);
            params.set("playlistId", arg1);
            var favIframeLink = $("#favs-wrapper iframe")[0].contentWindow.location.href;
            params.set("favs", favIframeLink);
            window.location.search = params.toString();
        },
        start: (arg1)=> {

            var params = new URLSearchParams(window.location.search);
            params.delete("playlistStart");
            var wantRandom = arg1==="RANDOM";
            if(!wantRandom) {
                params.set("playlistStart", arg1);
            }
            var favIframeLink = $("#favs-wrapper iframe")[0].contentWindow.location.href;
            params.set("favs", favIframeLink);
            window.location.search = params.toString();
        },
    } // urlChange

    function resizeIframeFavs() {
        var iframe = $("#favs-wrapper iframe")[0];
        iframe.style.height = iframe.contentWindow.document.documentElement.scrollHeight + 'px';
    }

    function isShuffleMode() {
        const isShuffling = window.location.href.indexOf("playlistStart=")===-1;
        console.log("isShuffleMode: ", isShuffling);
        return isShuffling;
    }

    $(()=>{
        if(!isShuffleMode()) {
            $("button#most-recent").addClass("active");
        }
    })
    </script>

    <script>
    // Overriding fav playlists
    $(()=>{
        // Instance
        var params = new URLSearchParams(window.location.search);
        var hasFavs = params.get("favs")!==null;
        if(hasFavs) {
            var path = params.get("favs");
            $("#favs").attr("src", path);
        } else {
            var path = "favs.php";
        }

        var tag = document.createElement("iframe");
        // tag.className.push("favs")
        tag.src = path;
        tag.id = "favs";
        document.getElementById("favs-wrapper").append( tag );
    });
    </script>

</head>
  <body>
    <div id="player"></div>
    <div class="spacer-v"></div>
    <div id="app-desc">By Weng. Play youtube videos from favorited channels and playlists in order or randomly. You can fit the video to the window so you can watch on one part of the screen. As an advance internet user, you can place a playlist ID in the URL param as <code>?playlistId=...</code> or start the playlist at a song position with the URL param like <code>&playlistStart=1</code>. As a developer, you can add more favorite channels or playlists at favs.php.</div>
    <div id="buttons">
        <button class="btn btn-default btn-sm" onclick="urlChange.start('RANDOM');"><i class="fa fa-random"></i> Next random</button>
        <button id="most-recent" class="btn btn-default-off btn-sm" onclick="urlChange.start(1); $(this).addClass('active');"><i class="fa fa-list-ol"></i> Most recent</button>
        <button class="btn btn-default-off btn-sm" onclick="$('#player').toggleClass('maximized');"><i class="fa fa-maximize"></i> Fit Video</button>
    </div>
    <div id="favs-wrapper"></div>
    <div id="discover-more">
        Discover new channels with: <a href="http://randomyt.com" target="_blank">RandomYt</a>.
    </div>

    <script>
    /** 
     *
     * If you had clicked Most recent, then do not shuffle
     * All next videos will be iterating from the first video
     *
     */
    var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // 3. This function creates an <iframe> (and YouTube player)
    //    after the API code downloads.
    function onYouTubeIframeAPIReady() {
            var player = new YT.Player("player", {
                height: '390',
                width: '640',
                playerVars: {
                    playsinline: 1,
                    // allowsInlineMediaPlayback: true,
                    listType:'playlist',
                    list:'<?php echo $playlistId; ?>',
                    <?php echo isset($playlistStartIndex)?"index:$playlistStartIndex,\n":""; ?>
                    autoplay: 1,
                },
                events: {
                    'onReady': function (event) {
                        //event.target.cuePlaylist({list: "PLFgquLnL59anYA8FwzqNFMp3KMcbKwMaT"});
                        //event.target.playVideo();

                        if(isShuffleMode()) {
                            event.target.mute();
                            setTimeout( function() { 
                                event.target.setShuffle(true); 
                                event.target.setLoop(true);
                                player.nextVideo();
                                event.target.unMute();
                            }, 100);
                        }
                    }
                }
            });
    } // onYouTubeIframeAPIReady
    </script>
  </body>
</html>