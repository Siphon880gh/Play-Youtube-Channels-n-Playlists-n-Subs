<?php
// Other defaults:
// Dua Lipa old music: OLAK5uy_n1RTVv-lJ6-4DjJmFUfF_MoRevOeYTM4Q
// Bebe Rexha old music: OLAK5uy_mc7x6vdCJWUpWXeF6NEY1ljjfwXoG18do
// RandomYt: PLJMMfLBtUHMlUCynG24upluI_pE9WFlII
// $defaultPlaylistId = "UU-J-KZfRV8c13fOCkhXdLiQ";
$defaultPlaylistId = "PLzg85AHZsA6Z0dmqF0A8LxVf1ojZZwfUm";
?>
<html>
<head>
    <title>Youtube Viewer and Shuffler by Weng</title>
    <meta property="fb:app_id" content="685633705547844" />
    <meta property="og:image" content="https://www.publicdomainpictures.net/pictures/250000/nahled/youtube-1515875370Xwr.jpg" />
    
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="assets/js/favs.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <!-- <script src="https://www.youtube.com/iframe_api"></script> -->
    
    <script>
    function jumpToActivePlaylist() {
        let $iframe = $("#favs-wrapper iframe");
        let $iframeContents = $iframe.contents();
        let $playlistLink = $iframeContents.find(`[data-playlist-id="${window.playlistId}"]`);

        // Calculate scroll position
        let scrollPosA = $iframe[0].offsetTop;
        let scrollPosB = $playlistLink[0].offsetTop;
        let scrollPos = scrollPosA + scrollPosB;

        // Jump to active playlist link and flash the link
        window.scrollTo(0, scrollPos);
        $playlistLink.animate({"font-weight": "900"}, 1000).delay(500).animate({"font-weight":"300"}, 2000)
        // debugger;
    }

    // (function js_php_Interface() {
        <?php
            $playlistId = ""; // will override
            if(isset($_GET["playlistId"]) && strlen($_GET["playlistId"])) {
                $playlistId = $_GET["playlistId"];
            } else {
                $playlistId = $defaultPlaylistId;
                
                if(isset($_GET["loop-video-id"]) && strlen($_GET["loop-video-id"]))
                    echo sprintf("window.history.pushState(null, 'defaultPlaylist', `?playlistId=%s&loop-video-id=%s`);", $playlistId, $_GET["loop-video-id"]);
                else
                    echo sprintf("window.history.pushState(null, 'defaultPlaylist', `?playlistId=%s`);", $playlistId);
            }

            $playlistStartIndex = ""; // default
            if( isset($_GET["playlistStart"]) && is_numeric(intval($_GET["playlistStart"])) ) {
                $num = intval($_GET["playlistStart"]);
                if($num<=2) $playlistStartIndex=-1;
                else $playlistStartIndex = $_GET["playlistStart"];
            }

            echo sprintf("
                    window.playlistId = '%s';
                    window.playlistStartIndex = '%s';
            ", $playlistId, $playlistStartIndex);
        ?>
    // })();
    window.urlChange = {
        playlist: (event, arg1)=> {

            // Get last 10
            let lastOpened = localStorage.getItem("YT__last-opened");
            let arrLastOpened = [];
            if(lastOpened) {
                arrLastOpened = JSON.parse(lastOpened);
            }

            if(event) {
                // Modify last 10
                let playlistName = event.target.innerText;
                arrLastOpened.filter(itrPlaylistName => itrPlaylistName !== playlistName);
                arrLastOpened.push(playlistName);
                arrLastOpened = arrLastOpened.slice(-10);

                // Update persistingly last 10
                localStorage.setItem("YT__last-opened", JSON.stringify(arrLastOpened));
            }

            // Change URL
            var params = new URLSearchParams(window.location.search);
            params.set("playlistId", arg1);

            // Set URL so can load the same extended playlist collection
            var favIframeLink = $("#favs-wrapper iframe")[0].contentWindow.location.href;
            var matchBundle = favIframeLink.match(new RegExp("favs=(.*)"));
            var matched = matchBundle&&matchBundle.length>1?matchBundle[1]:"default"; 
            if(matched) matched = matched.replaceAll(".php", "");

            params.set("favs", matched);
            window.location.search = params.toString();

        },
        start: (arg1)=> {

            var params = new URLSearchParams(window.location.search);
            params.delete("playlistStart");
            var wantRandom = arg1==="RANDOM";
            if(!wantRandom) {
                params.set("playlistStart", arg1);
            }

            // Set URL so can load the same extended playlist collection
            var favIframeLink = $("#favs-wrapper iframe")[0].contentWindow.location.href;
            var matchBundle = favIframeLink.match(new RegExp("favs=(.*)"));
            var matched = matchBundle&&matchBundle.length>1?matchBundle[1]:"default";
            if(matched) matched = matched.replaceAll(".php", "");

            params.set("favs", matched);
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
        // Set visual shuffle mnode button
        if(!isShuffleMode()) {
            $("button#most-recent").addClass("active");
        }
    })
    </script>

    <script>
    // Overriding fav playlists
    $(()=>{
        // URL settings
        var params = new URLSearchParams(window.location.search);
        var hasFavs = params.get("favs")!==null;
        if(hasFavs) {
            var path = "favs.php?favs=" + params.get("favs");
            // if(path.indexOf("favs/favs/")>=0) path = path.substr("favs/".length-1);
            // if(path.indexOf(".php")===-1) path += ".php";
            // if(path.indexOf("favs.php")>=0) path = "favs.php";
            $("#favs").attr("src", path);
        } else {
            var path = "favs.php";
        }

        var tag = document.createElement("iframe");
        // tag.className.push("favs")
        tag.src = path;
        tag.id = "favs";
        document.getElementById("favs-wrapper").append( tag );

        function refitVideoPrn() {
            // criteria met
            var isVideoHeightTooLargeForWindow = window.innerHeight < $("#player").height();
            var isVideoWidthTooLargeForWindow = window.innerWidth < $("#player").width();

            if(isVideoHeightTooLargeForWindow || isVideoWidthTooLargeForWindow) {
                $('#player').addClass('maximized');
                console.log("Triggered refit");
            }
        } // refitVideoPrn
        setInterval(refitVideoPrn, 1000);

    }); // on doc ready

    function openNextPlaylist() {
        var searchParams = (new URLSearchParams(window.location.search));
        var nextPlaylistId = searchParams.get("playlistId");

        if(nextPlaylistId!==null) {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id='${nextPlaylistId}']`);
            $b = $a.closest("li")
                .next("li")
                .find("a:nth-child(1)");

            $b.click();
            // debugger;
        } else {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id]`).first();
            searchParams.set("playlistId", $a.data("playlist-id"));
            var urlTrailing = searchParams.toString();
            window.location.search = urlTrailing;
        }
    } // openNextPlaylist

    function openPreviousPlaylist() {
        var searchParams = (new URLSearchParams(window.location.search));
        var nextPlaylistId = searchParams.get("playlistId");

        if(nextPlaylistId!==null) {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id='${nextPlaylistId}']`);
            $b = $a.closest("li")
                .prev("li")
                .find("a:nth-child(1)");

            $b.click();
            // debugger;
        } else {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id]`).first();
            searchParams.set("playlistId", $a.data("playlist-id"));
            var urlTrailing = searchParams.toString();
            window.location.search = urlTrailing;
        }
    } // openPreviousPlaylist
    </script>

</head>
  <body>

    <div id="player"></div>
    <div class="spacer-v"></div>
    <div id="app-desc" style="font-size:1.25rem;">Youtube Viewer and Shuffler<br/>By Weng. <span style="font-size:.8rem;">Youtube has changed their video algorithms and are not consistently showing my favorite subscriptions' most recent videos on the front page. With this app you can show the most recent videos from all your subscriptions and more. <a href="#" onclick="event.preventDefault(); $(this).next().show(); $(this).hide();">Read More</a><span style="display:none;">Play youtube videos from favorited channels and playlists in order or randomly. You can fit the video to the window so you can watch on one part of the screen. As an advance internet user, you can place a playlist ID in the URL param as <code>?playlistId=...</code> or start the playlist at a song position with the URL param like <code>&playlistStart=1</code>. As a developer, you can add more favorite channels or playlists at favs.php.</span></span></div>
    <div id="buttons">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-video"></i> <span>Video</span>
            </div>
            <div class="panel-body">
                <button id="most-recent" class="btn btn-default btn-sm" onclick="urlChange.start(1); $(this).addClass('active');"><i class="fa fa-list-ol"></i> Most recent</button>
                <button class="btn btn-default-off btn-sm" onclick="urlChange.start('RANDOM');"><i class="fa fa-random"></i> Next random</button>
                <span class="info-loop-group">
                    <button class="btn btn-default-off btn-sm loop-into-btn" onclick='window.location.href = window.location.search + "&loop-video-id=" + getVideoId();'><i class="fa fa-recycle"></i> Loop Video</button>
                    <button class="btn btn-default-off btn-sm loop-out-btn" onclick="window.history.back()" style="color:rgba(255,100,100,.6);"><i class="fa fa-sign-in-alt"></i> Exit Loop</button>
                </span>
                <button id="fit-video" class="btn btn-default-off btn-sm" onclick="$('html, body').scrollTop(0); $('#player').toggleClass('maximized'); event.stopPropagation();"><i class="fa fa-maximize"></i> Fit Video</button></br>
                <div style="width:1px; height:10px;"></div>
            </div>
        </div>

        <div class="panel panel-warning">
            <div class="panel-heading">
                <span onclick="More.counter('Playlistheader')"><i class="fa fa-list"></i> <span>Playlist</span></span>
            </div>
            <div class="panel-body">
                <button class="btn btn-default" onclick='openNextPlaylist()'><i id="random" class="fa fa-caret-right clickable"></i><span>&nbsp;Next</span></button>
                <button class="btn btn-default" onclick='openPreviousPlaylist()'><i id="random" class="fa fa-xs fa-undo clickable"></i><span>&nbsp;Prev</span></button>
                <button class="btn btn-default" onclick='$("#favs-wrapper iframe").contents().find("#random").click();'><i id="random" class="fa fa-random clickable"></i><span>&nbsp;Random</span></button>
                <button class="btn btn-secondary" onclick='jumpToActivePlaylist();'><i id="manual" class="fa fa-ruler-vertical clickable"></i><span>&nbsp;Scroll</span></button>
                <!-- Disabled Auth button: Reason for it was some music videos require sign in. But now disabled because "Age-restricted videos can’t be watched on most 3rd party websites" -->
                <!-- <button class="btn btn-secondary" onclick='window.open("https://accounts.google.com/signin/v2/identifier?service=youtube&uilel=3&passive=true&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26hl%3Den%26next%3Dhttps%253A%252F%252Fwww.youtube.com%252F&hl=en&ec=65620&flowName=GlifWebSignIn&flowEntry=ServiceLogin");' style="margin-top:5px;"><i id="manual" class="fa fa-user clickable"></i><span>&nbsp;Auth</span></button> -->
                <button class="btn btn-secondary" onclick='$("#favs-wrapper iframe").contents().find("#manual").click(); setTimeout(()=>{ $("#overridePlaylistId").focus(); }, 200);'><i id="manual" class="fa fa-cloud-upload-alt clickable"></i><span>&nbsp;Playlist ID</span></button>
            </div>
        </div>

    </div>

    </div>
    <div id="favs-wrapper" style="width:512px;">
    </div>
    <div id="discover-more">
        Discover new channels with: <a href="http://randomyt.com" target="_blank">RandomYt</a>.
    </div>

    <script>
    function overridePlaylistId() {
        var newPlaylistId = $('#overridePlaylistId').val();
        if(newPlaylistId.length) window.parent.urlChange.playlist(null, newPlaylistId);
    }
    </script>

    <!-- Modal: Override Playlist ID -->
    <div id="modal-override-playlist-id" class="modal modal-sm fade" style="left: 50%; transform: translateX(-50%); top:50%; translateY(-40%);">
        <div class="modal-dialog-off">
        
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Override playlist ID</h4>
                </div>
                <div class="modal-body" style="text-align:left;">
                    <input id="overridePlaylistId" type="text" style="margin-right:10px; margin-bottom:10px;"></input>
                    <button type="button" class="btn btn-primary" onclick="overridePlaylistId()">Run</button>
                    <span style="clear:both;"></span>
                </div>
            </div>

        </div>
    </div> <!-- /Modal -->

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
    // window.player;
    function getVideoId() {
        return player1.getVideoData()['video_id'];
    }

    function getVideoLoopingURL() {
        let params = new URLSearchParams(window.location.href);
    }

    var ytVideoLooper = `
    function onYouTubeIframeAPIReady() {
    window.player1 = new YT.Player("player", {
        height: '390',
        width: '640',
        videoId: loopVideoId, // Equivalent: player1.loadVideoById("3VqeJGcW680")
        events: {
            'onReady': function (event) {
                console.log("Using Youtube Iframe API with Youtube Video Looper algorithm ytVideoLooper");
                event.target.pauseVideo();
                        setTimeout( function() { 
                            event.target.setShuffle({'shufflePlaylist' : true}); 
                            event.target.setLoop(true); // if reaches end of playlist, can keep going
                            event.target.nextVideo();
                            event.target.playVideo();
                        }, 300);
            }, // end onReady
            'onStateChange': function(event) {
                // Tips on PlayerState: https://stackoverflow.com/questions/5957916/how-to-handle-youtube-video-events-started-finished-etc-in-uiwebview-ios
                if (event.data == YT.PlayerState.UNSTARTED) {
                    player1.playVideo();
                }   
                if (event.data == YT.PlayerState.ENDED) {
                    player1.playVideo();
                }   
            } // on statechange
        } // events
    }); // end Init Youtube player

} // end onYouTubeIframeAPIReady`;

    var ytPlaylist = `
    function onYouTubeIframeAPIReady() {
        // var shuffling = window.playlistStartIndex.length?true:false;
            window.player1 = new YT.Player("player", {
            height: '390',
            width: '640',
            playerVars: {
                playsinline: 1,
                // allowsInlineMediaPlayback: true,
                listType:'playlist',
                list: playlistId,
                index: window.playlistStartIndex.length?window.playlistStartIndex:null,
                autoplay: 1,
            },
            events: {
                'onReady': function (event) {
                    console.log("Using Youtube Iframe API with Youtube Playlist algorithm ytPlaylist");
                    if(isShuffleMode()) {
                        event.target.pauseVideo();
                        setTimeout( function() { 
                            event.target.setShuffle({'shufflePlaylist' : true}); 
                            event.target.setLoop(true); // if reaches end of playlist, can keep going
                            event.target.nextVideo();
                            event.target.playVideo();
                        }, 300);
                    } // end isShuffleMode
                }, // end onReady
                'onStateChange': function(event) {
                            if(YT.PlayerState.CUED) {
                                // console.log('Video ID: ', player1.getVideoData()['video_id']);
                                //player1.loadVideoById;
                                console.log( "State Change @ Video ID=" + getVideoId() );
                            }
                        } // onStateChange
            }
        }); // end Init Youtube player

    } // end onYouTubeIframeAPIReady`;



    // console.log("Debug Youtube Playlist API: ", ytPlaylist);
    // console.log("Debug Youtube Video Looping API: ", ytVideoLooper);
    // console.log("Debug Youtube API: If random video is not working, check the API code. Maybe the API has changed/updated.");
    // console.log("Debug Youtube API: Reference is at https://developers.google.com/youtube/iframe_api_reference")

    let params = new URLSearchParams(document.location.search);
    let loopVideoId = params.get("loop-video-id");

    if(loopVideoId===null) {
        $(".loop-out-btn").remove();
        eval(ytPlaylist);
    } else {
        $(".loop-into-btn").remove();
        eval(ytVideoLooper);
    }

    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>