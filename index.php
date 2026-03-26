
<?php
// Other defaults:
// $defaultPlaylistId = "OLAK5uy_mc7x6vdCJWUpWXeF6NEY1ljjfwXoG18do"; // Bebe Rexha old music
// $defaultPlaylistId = "OLAK5uy_n1RTVv-lJ6-4DjJmFUfF_MoRevOeYTM4Q"; // Dua Lipa old music
// $defaultPlaylistId = "UU-J-KZfRV8c13fOCkhXdLiQ"; // // Dua Lipa docs and music
// $defaultPlaylistId = "PLzg85AHZsA6Z0dmqF0A8LxVf1ojZZwfUm"; // Keep the beat going
$defaultPlaylistId = "PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda";
?>
<html>
<head>
    <title>Youtube Viewer and Shuffler by Weng</title>
    <meta property="fb:app_id" content="685633705547844" />
    <meta property="og:image" content="https://www.publicdomainpictures.net/pictures/250000/nahled/youtube-1515875370Xwr.jpg" />
    
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="assets/js/favs.js?v=2"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <!-- <script src="https://www.youtube.com/iframe_api"></script> -->

    <style>
    /** Placed here for now since Flobro cache can't clear */
    .d-none {
        display: none;
    }
    </style>
    
    <script>
    // Fit video?
        let a = new URLSearchParams(window.location.search).get("fit-video")
        if(a!==null) {
            setTimeout(()=>{
                $("#fit-video").click();
            }, 500)
        }

    function utils__getPlaylistIdFromURL() {
        return String(new URLSearchParams(window.location.search).get("playlistId"));
    }

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
            // if( isset($_GET["playlistStart"]) && is_numeric(intval($_GET["playlistStart"])) ) {
            if( isset($_GET["playlistStart"]) ) {
                $num = intval($_GET["playlistStart"]);
                // if($num<=2) $playlistStartIndex=-1;
                // else $playlistStartIndex = $_GET["playlistStart"];
                $playlistStartIndex = $num;
            }

            echo sprintf("
                    window.playlistId = '%s';
                    window.playlistStartIndex = '%s';
            ", $playlistId, $playlistStartIndex);
        ?>

    // })();

    // Flash playlist name at the top
    $(()=>{
        setTimeout(()=>{

            var playlistName = (()=>{
                let u = (new URLSearchParams(window.location.search)).get("playlistId");
                let e = null;
                if(u) {
                    e = $("#favs-wrapper iframe").contents().find(`[data-playlist-id='${u}']`);
                }
                if(e) {
                    return e.text();
                } else {
                    return "";
                }

            })();
            $("#playlist-name-flash").removeClass("d-none").text(playlistName)
        }, 3000);
        setTimeout(()=>{
            $("#playlist-name-flash").addClass("d-none")
        }, 10000);
    });

    // Get last 10
    let lastOpened = localStorage.getItem("YT__last-opened");
    window.arrLastOpened = [];
    if(lastOpened) {
        arrLastOpened = JSON.parse(lastOpened);
    }

    if(window.playlistId) {
        // Obsolete: Only add playlist to be crossed out on next app opening if you watched >10 seconds
        // because you might missclick a playlist or decided not to watch it and the purpose of crossing 
        // out playlists is so that you know it's been explored for the week.

        setTimeout(()=>{
            // Equivalent: Push playlist Id to the top if it exists or inserts to the top if it doesn't
            arrLastOpened = arrLastOpened.filter(aPlayerlistId => aPlayerlistId !== window.playlistId);
            arrLastOpened.push(window.playlistId);

            // Keep track of only 10 crossed out playlists
            // arrLastOpened = arrLastOpened.slice(-10);
            // debugger;

            var lastWatchedList = JSON.stringify(arrLastOpened);
            localStorage.setItem("YT__last-opened", lastWatchedList);
            renderCrossedOutPlaylists(arrLastOpened);
            // console.log("Updated last watched list: " + lastWatchedList);
        }, 10000);
    }


    window.urlChange = {
        playlist: ($el, playlistId)=> {

            // Change URL
            var params = new URLSearchParams(window.location.search);
            params.delete("loop-video-id"); // User clicked Most recent or Next random, so no more looping
            params.set("playlistId", playlistId);

            // Set URL so can load the same extended playlist collection
            var favIframeLink = $("#favs-wrapper iframe")[0].contentWindow.location.href;
            var matchBundle = favIframeLink.match(new RegExp("favs=(.*)"));
            var matched = matchBundle&&matchBundle.length>1?matchBundle[1]:"Default"; 
            if(matched) matched = matched.replaceAll(".php", "");

            params.set("favs", matched);
            window.location.search = params.toString();

        },
        start: (arg1)=> {

            var params = new URLSearchParams(window.location.search);
            params.delete("loop-video-id"); // User clicked Most recent or Next random, so no more looping
            params.delete("playlistStart");

            var userWantsRandom = arg1==="RANDOM";
            if(!userWantsRandom) {
                params.set("playlistStart", arg1);
            }

            // Set URL so can load the same extended playlist collection
            var favIframeLink = $("#favs-wrapper iframe")[0].contentWindow.location.href;
            var matchBundle = favIframeLink.match(new RegExp("favs=(.*)"));
            var matched = matchBundle&&matchBundle.length>1?matchBundle[1]:"Default";
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
        // let isShuffling = window.location.href.indexOf("playlistStart=")===-1;

        // If first opening app, default is no shuffling
        if(localStorage.getItem("YT__shuffle_mode")===null)
        localStorage.getItem("YT__shuffle_mode")==="0";

        let isShuffling = localStorage.getItem("YT__shuffle_mode")==="1";
        console.log("isShuffleMode: ", isShuffling);
        return isShuffling;
    }

    $(()=>{
        // Set visual shuffle mnode button
        if(!isShuffleMode()) {
            $("#most-recent").addClass("active");
        }
        // debugger;
    })
    
    // Pre-search
    $(()=>{
        var paramsSearch = new URLSearchParams(window.location.search);
        var search = paramsSearch.get("search");
        if(search) {
            setTimeout(()=>{
                let $filter = $("#favs").contents().find("#playlist-filter");
                $filter.val(search);
                $filter.trigger("input");
                console.log(`Search: ${search}`);
            }, 1000);
        }
    })

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

    function intern_skipOverNonLink($current) {
        $current = $current.closest("li")
                .next("li")
                .find("a:nth-child(1)");

        if($current.hasClass("not-link")) {
            $current = intern_skipOverNonLink($current)
        }

        return $current;
    }

    function intern_backTrackOverNonLink($current) {
        $current = $current.closest("li")
                .prev("li")
                .find("a:nth-child(1)");

        if($current.hasClass("not-link")) {
            $current = intern_backTrackOverNonLink($current)
        }

        return $current;
    }

    function openNextPlaylist() {
        var searchParams = (new URLSearchParams(window.location.search));
        var nextPlaylistId = searchParams.get("playlistId");

        if(nextPlaylistId!==null) {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id='${nextPlaylistId}']`);
            var $b = $a.closest("li")
                .next("li")
                .find("a:nth-child(1)");

            // Check if already at last item
            if($b.length===0) {
                $b = $("#favs-wrapper iframe").contents().find(".playlists-target li:not(.not-link)").eq(0);
                $b = $b.find("a:nth-child(1)");

            }

            // Check if on a separator, or basically on a non-link element
            if($b.hasClass("not-link")) {
                $b = intern_skipOverNonLink($b)
            }

            $b.trigger("click");
            // debugger;
        } else {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id]`).first();
            debugger;
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

            // Check if already at first item
            if($b.length===0) {
                $b = $("#favs-wrapper iframe").contents().find(".playlists-target li:not(.not-link)").last();
                $b = $b.find("a:nth-child(1)");

            }

            // Check if on a separator, or basically on a non-link element
            if($b.hasClass("not-link")) {
                $b = intern_backTrackOverNonLink($b)
            }

            $b.click();
            // debugger;
        } else {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id]`).first();
            searchParams.set("playlistId", $a.data("playlist-id"));
            var urlTrailing = searchParams.toString();
            window.location.search = urlTrailing;
        }
    } // openPreviousPlaylist
    function setShuffleMode(wantShuffled) {
        var $mostRecent = $("#most-recent");
        if(wantShuffled) {
            $mostRecent.addClass("active"); // most recent
            localStorage.setItem("YT__shuffle_mode", 0); // no shuffling
        } else {
            $mostRecent.removeClass("active"); // not most recent
            localStorage.setItem("YT__shuffle_mode", 1); // shuffling
        }
    }
    
    function handleLoopBtn($this) {
        if($this.hasClass("active")) {
            $this.removeClass("active");
            window.history.back();
        } else {
            $this.addClass("active");
            window.location.href = window.location.search + "&loop-video-id=" + getVideoId();
        }
    }

    function getCurrentYoutubeUrl() {
        let params = new URLSearchParams(window.location.search);
        let activePlaylistId = params.get("playlistId") || window.playlistId;
        let activeVideoId = params.get("loop-video-id");

        if(activeVideoId && activePlaylistId) {
            return "https://www.youtube.com/watch?v=" + encodeURIComponent(activeVideoId) + "&list=" + encodeURIComponent(activePlaylistId);
        }

        if(activePlaylistId) {
            return "https://www.youtube.com/playlist?list=" + encodeURIComponent(activePlaylistId);
        }

        if(activeVideoId) {
            return "https://www.youtube.com/watch?v=" + encodeURIComponent(activeVideoId);
        }

        return "https://www.youtube.com/";
    }

    function openPlaylistOnYoutube() {
        window.open(getCurrentYoutubeUrl(), "_blank", "noopener");
    }

    function showPlayerFallback(reason) {
        let label = "Embedded playback hit a YouTube player restriction.";
        if(reason === 5 || reason === "5") {
            label = "Embedded playback failed in the YouTube HTML5 player.";
        } else if(reason === 100 || reason === "100") {
            label = "The video is no longer available.";
        } else if(reason === 101 || reason === "101" || reason === 150 || reason === "150") {
            label = "The video owner blocked playback on other sites.";
        }

        $("#player-fallback")
            .removeClass("d-none")
            .find(".fallback-reason")
            .text(label);
    }

    function setPlaylistPositionIndicator(text, isVisible) {
        let $indicator = $("#playlist-position-indicator");
        let $label = $indicator.find(".playlist-position-label");
        if(!$indicator.length || !$label.length) {
            return;
        }

        if(isVisible) {
            $label.text(text);
            $indicator.removeClass("d-none");
        } else {
            $indicator.addClass("d-none");
            $label.text("");
        }
    }

    function getPlaylistPositionDetails() {
        if(!window.player1) {
            return null;
        }

        let playlist;
        let playlistIndex;
        let videoId = "";

        try {
            playlist = window.player1.getPlaylist();
            playlistIndex = window.player1.getPlaylistIndex();
            videoId = getVideoId();
        } catch(e) {
            return null;
        }

        if(!Array.isArray(playlist) || playlist.length === 0) {
            return null;
        }

        if(typeof playlistIndex !== "number" || playlistIndex < 0) {
            playlistIndex = playlist.indexOf(videoId);
        }

        if(typeof playlistIndex !== "number" || playlistIndex < 0) {
            return null;
        }

        return {
            playlist: playlist,
            playlistIndex: playlistIndex,
            videoId: videoId
        };
    }

    function openPreviousVideoInPlaylist() {
        let details = getPlaylistPositionDetails();
        if(!details) {
            return;
        }

        try {
            if(details.playlistIndex <= 0) {
                window.player1.seekTo(0, true);
                window.player1.playVideo();
            } else {
                window.player1.playVideoAt(details.playlistIndex - 1);
            }
        } catch(e) {
            console.log("Unable to open previous video in playlist", e);
        }

        queuePlaylistPositionIndicatorRefresh();
    }

    function openFirstVideoInPlaylist() {
        let details = getPlaylistPositionDetails();
        if(!details) {
            return;
        }

        try {
            window.player1.playVideoAt(0);
        } catch(e) {
            console.log("Unable to open first video in playlist", e);
        }

        queuePlaylistPositionIndicatorRefresh();
    }

    function openNextVideoInPlaylist() {
        let details = getPlaylistPositionDetails();
        if(!details) {
            return;
        }

        try {
            let isLastVideo = details.playlistIndex >= details.playlist.length - 1;
            if(isLastVideo) {
                if(window.confirm("Loop back to the first video in this playlist?")) {
                    window.player1.playVideoAt(0);
                }
            } else {
                window.player1.playVideoAt(details.playlistIndex + 1);
            }
        } catch(e) {
            console.log("Unable to open next video in playlist", e);
        }

        queuePlaylistPositionIndicatorRefresh();
    }

    function refreshPlaylistPositionIndicator() {
        let details = getPlaylistPositionDetails();
        if(!details) {
            setPlaylistPositionIndicator("", false);
            return;
        }

        let label = "Playlist position " + (details.playlistIndex + 1) + " / " + details.playlist.length;
        if(isShuffleMode()) {
            label += " shuffled";
        }

        setPlaylistPositionIndicator(label, true);
    }

    function queuePlaylistPositionIndicatorRefresh() {
        [0, 250, 1000].forEach((delay)=>{
            setTimeout(refreshPlaylistPositionIndicator, delay);
        });
    }

    function handlePlayerError(event) {
        let errorCode = event && typeof event.data !== "undefined" ? event.data : "";
        console.log("YouTube player error", errorCode);
        showPlayerFallback(errorCode);
        queuePlaylistPositionIndicatorRefresh();

        if(loopVideoId !== null) {
            return;
        }

        if(window.player1 && typeof window.player1.nextVideo === "function") {
            setTimeout(()=>{
                try {
                    window.player1.nextVideo();
                } catch(e) {
                    console.log("Unable to advance after player error", e);
                }
            }, 800);
        }
    }

    window.ytDiagnostics = {
        lastState: null,
        lastStateName: "",
        lastStateAt: 0,
        lastProgressAt: 0,
        lastCurrentTime: null,
        lastVideoId: "",
        watchdogId: null,
        lastWatchdogLogAt: 0
    };

    function getPlayerStateName(stateCode) {
        if(typeof YT === "undefined" || !YT.PlayerState) {
            return String(stateCode);
        }

        switch(stateCode) {
            case YT.PlayerState.UNSTARTED: return "UNSTARTED";
            case YT.PlayerState.ENDED: return "ENDED";
            case YT.PlayerState.PLAYING: return "PLAYING";
            case YT.PlayerState.PAUSED: return "PAUSED";
            case YT.PlayerState.BUFFERING: return "BUFFERING";
            case YT.PlayerState.CUED: return "CUED";
            default: return String(stateCode);
        }
    }

    function getPlayerSnapshot() {
        if(!window.player1) {
            return { hasPlayer: false };
        }

        let snapshot = { hasPlayer: true };

        try { snapshot.state = window.player1.getPlayerState(); } catch(e) { snapshot.stateError = String(e); }
        try { snapshot.stateName = getPlayerStateName(snapshot.state); } catch(e) { snapshot.stateNameError = String(e); }
        try { snapshot.videoId = getVideoId(); } catch(e) { snapshot.videoIdError = String(e); }
        try { snapshot.currentTime = window.player1.getCurrentTime(); } catch(e) { snapshot.currentTimeError = String(e); }
        try { snapshot.duration = window.player1.getDuration(); } catch(e) { snapshot.durationError = String(e); }
        try { snapshot.playlistIndex = window.player1.getPlaylistIndex(); } catch(e) { snapshot.playlistIndexError = String(e); }
        try { snapshot.playlist = window.player1.getPlaylist(); } catch(e) { snapshot.playlistError = String(e); }
        if(Array.isArray(snapshot.playlist)) {
            snapshot.playlistLength = snapshot.playlist.length;
            delete snapshot.playlist;
        }

        return snapshot;
    }

    function logPlayerStateChange(event, source) {
        let stateCode = event && typeof event.data !== "undefined" ? event.data : null;
        let stateName = getPlayerStateName(stateCode);
        let snapshot = getPlayerSnapshot();

        window.ytDiagnostics.lastState = stateCode;
        window.ytDiagnostics.lastStateName = stateName;
        window.ytDiagnostics.lastStateAt = Date.now();

        if(snapshot.videoId) {
            window.ytDiagnostics.lastVideoId = snapshot.videoId;
        }

        if(stateCode === YT.PlayerState.PLAYING) {
            window.ytDiagnostics.lastProgressAt = Date.now();
            if(typeof snapshot.currentTime === "number") {
                window.ytDiagnostics.lastCurrentTime = snapshot.currentTime;
            }
        }

        console.log("[YT] state", {
            source,
            stateCode,
            stateName,
            snapshot
        });

        queuePlaylistPositionIndicatorRefresh();
    }

    function startPlayerWatchdog() {
        if(window.ytDiagnostics.watchdogId !== null) {
            clearInterval(window.ytDiagnostics.watchdogId);
        }

        window.ytDiagnostics.watchdogId = setInterval(()=>{
            if(!window.player1 || typeof window.player1.getPlayerState !== "function") {
                return;
            }

            let snapshot = getPlayerSnapshot();
            refreshPlaylistPositionIndicator();
            let now = Date.now();

            if(typeof snapshot.currentTime === "number") {
                if(window.ytDiagnostics.lastCurrentTime === null || snapshot.currentTime > window.ytDiagnostics.lastCurrentTime + 0.25) {
                    window.ytDiagnostics.lastCurrentTime = snapshot.currentTime;
                    window.ytDiagnostics.lastProgressAt = now;
                }
            }

            let state = snapshot.state;
            let noProgressForMs = window.ytDiagnostics.lastProgressAt ? (now - window.ytDiagnostics.lastProgressAt) : 0;
            let shouldLogStall = false;

            if(state === YT.PlayerState.PLAYING && noProgressForMs > 12000) {
                shouldLogStall = true;
            }

            if(state === YT.PlayerState.BUFFERING && noProgressForMs > 15000) {
                shouldLogStall = true;
            }

            if((state === YT.PlayerState.UNSTARTED || state === YT.PlayerState.PAUSED) && noProgressForMs > 10000) {
                shouldLogStall = true;
            }

            if(shouldLogStall && now - window.ytDiagnostics.lastWatchdogLogAt > 10000) {
                window.ytDiagnostics.lastWatchdogLogAt = now;
                console.error("[YT] playback stalled or stopped unexpectedly", {
                    lastState: window.ytDiagnostics.lastStateName,
                    noProgressForMs,
                    snapshot
                });
            }
        }, 4000);
    }
    </script>

    <style>
    .loop-btn:not(.active) i::before {
        content: "\f1b8";
    } 
    .loop-btn:not(.active)::after {
        content: " Loop Video";
    } 
    .loop-btn.active i::before {
        content: "\f2f6";
    } 
    .loop-btn.active::after {
        content: " Exit Loop";
    } 
    .loop-btn.active {
        color:rgba(255,100,100,.6)
    } 
    </style>

</head>
  <body>
    <div id="playlist-name-flash" class="d-none"></div>

    <div id="player"></div>
    <div id="playlist-position-indicator" class="d-none" role="group" aria-label="Playlist navigation">
        <button type="button" class="playlist-position-arrow playlist-position-arrow-first" onclick="openFirstVideoInPlaylist();" aria-label="First video">
            <span>1</span>
        </button>
        <button type="button" class="playlist-position-arrow playlist-position-arrow-prev" onclick="openPreviousVideoInPlaylist();" aria-label="Previous video">
            <i class="fa fa-chevron-left"></i>
        </button>
        <span class="playlist-position-label"></span>
        <button type="button" class="playlist-position-arrow playlist-position-arrow-next" onclick="openNextVideoInPlaylist();" aria-label="Next video">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
    <div id="player-fallback" class="alert alert-warning d-none" style="margin: 10px auto 0 auto; max-width: 640px;">
        <span class="fallback-reason">Embedded playback hit a YouTube player restriction.</span>
        <button class="btn btn-warning btn-xs" onclick="openPlaylistOnYoutube();" style="margin-left:10px;">Open YouTube</button>
    </div>
    <div class="spacer-v"></div>

    <div class="container-custom p-20">
        <div id="app-meta">
            <details id="app-desc" class="app-disclosure">
                <summary>
                    <span class="app-disclosure-label">About this app</span>
                    <span class="app-disclosure-preview">Youtube Viewer and Shuffler</span>
                </summary>
                <div class="app-disclosure-body">
                    <strong>Youtube Viewer and Shuffler</strong><br/>
                    By Weng. I can track my favorite channels and playlists. Why? Youtube changed how new videos show on my feed. Clicking a video just out of curiosity affects subsequent home views from showing my subscriptions. So something has gotta be done! Play youtube videos from favorited channels and playlists in order or randomly. You can fit the video to the window so you can watch on one part of the screen. As an advance internet user, you can place a playlist ID in the URL param as <code>?playlistId=...</code> or start the playlist at a song position with the URL param like <code>&playlistStart=1</code>. Want the video player fitted to the window when the page loads? Use URL parameter <code>fit-video</code>. As a developer, you can add more favorite channels or playlists at favs.php and /favs.
                </div>
            </details>
            <details id="funding-needed" class="app-disclosure app-disclosure-support">
                <summary>
                    <span class="app-disclosure-label">Roadmap / support</span>
                    <span class="app-disclosure-preview">Funding, playlist editor, OAuth2</span>
                </summary>
                <div class="app-disclosure-body">
                    Funding needed. If only I have some time, I'll add an interface to add playlists or channels. Right now it's done through a JSON file on my side. I'd also add OAuth2 for your own profiles. Please <a href="https://www.wengindustry.com/me/contact" target="_blank" rel="noopener noreferrer">let me know</a> if you express interest in scaling up the app. Your name will be added and special requests possible.
                </div>
            </details>
        </div>
        <div id="buttons">
            <div class="panel panel-warning control-card control-card-video"  style="border-color:rgba(200,0,0,.75);">
                <div class="panel-heading" style="background-color:rgba(200,0,0,.35); border-color:rgba(200,0,0,.75);">
                    <i class="fa fa-video"></i> <span>Video</span>
                </div>
                <div class="panel-body">
                    <button id="most-recent" class="btn btn-default btn-sm control-btn control-btn-most-recent" onclick="setShuffleMode(true); urlChange.start(1); $(this).addClass('active');"><i class="fa"></i> Most recent</button>
                    <button class="btn btn-default-off btn-sm control-btn control-btn-next-random" onclick="setShuffleMode(false); urlChange.start('RANDOM');"><i class="fa fa-random"></i> Next random</button>
                    <span class="info-loop-group">
                        <button class="btn btn-default-off btn-sm loop-btn control-btn control-btn-loop" onclick='handleLoopBtn($(this));'><i class="fa"></i></button>
                    </span>
                    <button id="fit-video" class="btn btn-default-off btn-sm control-btn control-btn-fit-video" onclick="$('html, body').scrollTop(0); $('#player').toggleClass('maximized'); event.stopPropagation();"><i class="fa fa-maximize"></i> Fit Video</button>
                </div>
            </div>

            <div class="panel panel-warning control-card control-card-playlist" style="border-color:rgba(196,125,123,.75);">
                <div class="panel-heading" style="background-color:rgba(230,125,123,.75); color:rgba(255,255,255,0.75);">
                    <span onclick="More.counter('Playlistheader')"><i class="fa fa-list"></i> <span>Playlist</span></span>
                </div>
                <div class="panel-body">
                    <button class="btn btn-default control-btn control-btn-next" onclick='openNextPlaylist()'><i id="random" class="fa fa-caret-right clickable"></i><span>&nbsp;Next</span></button>
                    <button class="btn btn-default control-btn control-btn-prev" onclick='openPreviousPlaylist()'><i id="random" class="fa fa-xs fa-undo clickable"></i><span>&nbsp;Prev</span></button>
                    <button class="btn btn-default control-btn control-btn-random" onclick='$("#favs-wrapper iframe").contents().find("#random").click();'><i id="random" class="fa fa-random clickable"></i><span>&nbsp;Random</span></button>
                    <button class="btn btn-default control-btn control-btn-open-youtube" onclick='openPlaylistOnYoutube();'><i class="fa fa-external-link-alt clickable"></i><span>&nbsp;Open YouTube</span></button>
                    <button class="btn btn-secondary control-btn control-btn-playlist-id" onclick='$("#override-playlist-ui").toggleClass("fade d-none"); setTimeout(()=>{ $("#overridePlaylistId").focus(); }, 200);'><i id="manual" class="fa fa-cloud-upload-alt clickable"></i><span>&nbsp;Playlist ID</span></button>
                    <button class="btn btn-secondary control-btn control-btn-scroll" onclick='jumpToActivePlaylist();'><i id="manual" class="fa fa-ruler-vertical clickable"></i><span>&nbsp;Scroll</span></button>
                    <!-- Disabled Auth button: Reason for it was some music videos require sign in. But now disabled because "Age-restricted videos can’t be watched on most 3rd party websites" -->
                    <!-- <button class="btn btn-secondary" onclick='window.open("https://accounts.google.com/signin/v2/identifier?service=youtube&uilel=3&passive=true&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26hl%3Den%26next%3Dhttps%253A%252F%252Fwww.youtube.com%252F&hl=en&ec=65620&flowName=GlifWebSignIn&flowEntry=ServiceLogin");' style="margin-top:5px;"><i id="manual" class="fa fa-user clickable"></i><span>&nbsp;Auth</span></button> -->
                </div>
                <div id="override-playlist-ui" class="panel-body fade d-none" style="padding:7.5px; border-radius:5px; text-align:center; width:fit-content; margin:0 auto 10px auto; border:1px solid lightgray;">
                    <div class="modal-header" style="padding:5px; border-bottom:0;">
                        <h4 class="modal-title">Override playlist ID</h4>
                    </div>
                    <div class="modal-body" style="text-align:center; padding: 5px;">
                        <input id="overridePlaylistId" type="text" style="margin-right:10px; margin-bottom:10px;"></input>
                        <button type="button" class="btn btn-primary" onclick="overridePlaylistId()">Run</button>
                        <span style="clear:both;"></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- </div> --> <!-- Extra div? -->
        <div id="favs-wrapper">
        </div>
        <div id="discover-more">
            Discover new channels at: <a href="http://randomyt.com" target="_blank" rel="noopener noreferrer">RandomYt</a>.
        </div>

    </div> <!-- p-20 custom container -->

    <script>
    function overridePlaylistId() {
        var newPlaylistId = $('#overridePlaylistId').val();
        if(newPlaylistId.length) window.parent.urlChange.playlist(null, newPlaylistId);
    }
    </script>

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

    function getVideoIndexOrNull() {
        // return null;
        const a = window.playlistStartIndex.length?window.playlistStartIndex:null;
        const b = localStorage.getItem("YT__playlistWantsShuffle")==="1";
        if(b) {
            localStorage.removeItem("YT__playlistWantsShuffle");
            return null;
        }

        return a;
    } // getVideoIndexOrNull

    var ytVideoLooper = `
    function onYouTubeIframeAPIReady() {
    window.player1 = new YT.Player("player", {
        height: '390',
        width: '640',
        videoId: loopVideoId, // Equivalent: player1.loadVideoById("3VqeJGcW680")
        events: {
            'onReady': function (event) {
                console.log("Using Youtube Iframe API with Youtube Video Looper algorithm ytVideoLooper");
                $("#player-fallback").addClass("d-none");
                startPlayerWatchdog();
                logPlayerStateChange({data: event.target.getPlayerState()}, "videoLooper:onReady");
                event.target.pauseVideo();
                        setTimeout( function() { 
                            event.target.setShuffle(true);
                            event.target.setLoop(true); // if reaches end of playlist, can keep going
                            event.target.nextVideo();
                            event.target.playVideo();
                        }, 300);
            }, // end onReady
            'onStateChange': function(event) {
                logPlayerStateChange(event, "videoLooper:onStateChange");
                // Tips on PlayerState: https://stackoverflow.com/questions/5957916/how-to-handle-youtube-video-events-started-finished-etc-in-uiwebview-ios
                if (event.data == YT.PlayerState.UNSTARTED) {
                    player1.playVideo();
                }   
                if (event.data == YT.PlayerState.ENDED) {
                    player1.playVideo();
                }   
            }, // on statechange
            'onError': handlePlayerError
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
                    $("#player-fallback").addClass("d-none");
                    startPlayerWatchdog();
                    logPlayerStateChange({data: event.target.getPlayerState()}, "playlist:onReady");
                    if(isShuffleMode() || getVideoIndexOrNull()===null) {
                        event.target.pauseVideo();
                        setTimeout( function() { 
                            event.target.setShuffle(true);
                            event.target.setLoop(true); // if reaches end of playlist, can keep going
                            event.target.nextVideo();
                            event.target.playVideo();
                        }, 300);
                    } // end isShuffleMode
                }, // end onReady
                'onStateChange': function(event) {
                            logPlayerStateChange(event, "playlist:onStateChange");
                            if(event.data === YT.PlayerState.CUED) {
                                // console.log('Video ID: ', player1.getVideoData()['video_id']);
                                //player1.loadVideoById;
                                console.log( "State Change @ Video ID=" + getVideoId() );
                            }
                        }, // onStateChange
                'onError': handlePlayerError
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
        $(".loop-btn").removeClass("active");
        eval(ytPlaylist);
    } else {
        $(".loop-btn").addClass("active");
        eval(ytVideoLooper);
    }

    // Give time for user to read error (age related restriction, etc), then play next video
    setTimeout(()=>{
        if($("#player .ytp-error").length!==0) {
            handlePlayerError({data: "dom-error"});
        }
    },  3500)
    
    // If from a bookmark that has search playlist keywords
    $(()=>{
        let params = new URLSearchParams(document.location.search);
        let search = params.get("search");
        if(search)
            $("#playlist-filter").val(search).trigger("input")
    });
    </script>

    <script src="assets/js/app.js?v=2"></script>
  </body>
</html>