
<?php
// Other defaults:
// $defaultPlaylistId = "OLAK5uy_mc7x6vdCJWUpWXeF6NEY1ljjfwXoG18do"; // Bebe Rexha old music
// $defaultPlaylistId = "OLAK5uy_n1RTVv-lJ6-4DjJmFUfF_MoRevOeYTM4Q"; // Dua Lipa old music
// $defaultPlaylistId = "UU-J-KZfRV8c13fOCkhXdLiQ"; // // Dua Lipa docs and music
// $defaultPlaylistId = "PLzg85AHZsA6Z0dmqF0A8LxVf1ojZZwfUm"; // Keep the beat going
$defaultPlaylistId = "PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda";
// YouTube embeds require a usable Referer (Error 153 otherwise, especially on iOS Safari).
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
<html>
<head>
    <title>Youtube Viewer and Shuffler by Weng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta property="fb:app_id" content="685633705547844" />
    <meta property="og:image" content="https://www.publicdomainpictures.net/pictures/250000/nahled/youtube-1515875370Xwr.jpg" />
    
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="assets/js/favs.js?v=2"></script>
    <?php include __DIR__ . "/env/icon-fonts.php"; ?>
    <script src="env/playlist-icons.js?v=8"></script>
    <link rel="stylesheet" href="assets/css/index.css?v=34">
    <link rel="stylesheet" href="assets/css/collection-themes.css?v=8">
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

    // Floating "Scroll to top" button; jumps instantly when the user prefers reduced motion
    function scrollToPageTop() {
        let reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" });
    }

    // Space left between the sticky "All topics" nav and a playlist scrolled to just below it
    const STICKY_NAV_GAP = 13;

    // Page scroll position that puts an element of the favs iframe just below the sticky nav.
    // The nav's height varies (stuck border, a current-topic label that wraps on phones), so it's measured live.
    function favsElementScrollTarget(el) {
        try {
            let iframe = $("#favs-wrapper iframe")[0];
            if(!iframe || !el) {
                return null;
            }
            // The iframe is sized to its content, so an element's position in it maps straight onto the page
            let elPageTop = iframe.getBoundingClientRect().top + el.getBoundingClientRect().top + window.scrollY;
            let nav = document.getElementById("section-nav-parent");
            let navHeight = nav ? nav.getBoundingClientRect().height : 0;
            return Math.max(Math.round(elPageTop - navHeight - STICKY_NAV_GAP), 0);
        } catch(e) {
            return null;
        }
    }

    // Scroll to an element of the favs list, then correct once after the scroll settles, since the nav
    // can change height on arrival when its current-topic label updates
    let favsScrollToken = 0;
    function scrollPageToFavsElement(el, smooth, onDone) {
        let target = favsElementScrollTarget(el);
        if(target === null) {
            return false;
        }

        let token = ++favsScrollToken;
        let reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        window.scrollTo({ top: target, behavior: (smooth && !reduceMotion) ? "smooth" : "auto" });

        // If the user takes over scrolling, leave the page alone
        let cancelled = false;
        let cancel = function() { cancelled = true; };
        let events = ["wheel", "touchstart", "keydown", "mousedown"];
        events.forEach(function(name) { window.addEventListener(name, cancel, { passive: true }); });
        let finish = function() {
            events.forEach(function(name) { window.removeEventListener(name, cancel); });
            if(onDone) {
                onDone();
            }
        };

        let lastY = -1;
        let stableFrames = 0;
        let started = performance.now();
        let waitForSettle = function() {
            if(token !== favsScrollToken || cancelled) {
                return finish();
            }
            let y = window.scrollY;
            stableFrames = Math.abs(y - lastY) < 1 ? stableFrames + 1 : 0;
            lastY = y;
            if(stableFrames < 6 && performance.now() - started < 2500) {
                requestAnimationFrame(waitForSettle);
                return;
            }
            // Give the nav's scroll-spy a moment to update its label, then correct once
            setTimeout(function() {
                if(token === favsScrollToken && !cancelled) {
                    let corrected = favsElementScrollTarget(el);
                    if(corrected !== null && Math.abs(corrected - window.scrollY) > 2) {
                        window.scrollTo(0, corrected);
                    }
                }
                finish();
            }, 150);
        };
        requestAnimationFrame(waitForSettle);
        return true;
    }

    // Where jumpToActivePlaylist() scrolls to, or null when the active playlist isn't on the page
    function activePlaylistScrollTarget() {
        try {
            let $iframe = $("#favs-wrapper iframe");
            if(!$iframe.length || !window.playlistId) {
                return null;
            }
            let $playlistLink = $iframe.contents().find(`[data-playlist-id="${window.playlistId}"]`);
            if(!$playlistLink.length) {
                return null;
            }

            let top = favsElementScrollTarget($playlistLink[0]);
            return top === null ? null : { top: top, $link: $playlistLink };
        } catch(e) {
            return null;
        }
    }

    function jumpToActivePlaylist() {
        let target = activePlaylistScrollTarget();
        if(!target) {
            updateScrollArrowButtons();
            return;
        }

        // Jump to active playlist link and flash the link
        scrollPageToFavsElement(target.$link[0], false, updateScrollArrowButtons);
        target.$link.animate({"font-weight": "900"}, 1000).delay(500).animate({"font-weight":"300"}, 2000)
        updateScrollArrowButtons();
    }

    // Floating up/down arrows hide when they'd do nothing: already at the top, or already at the playlist
    const SCROLL_ARROW_THRESHOLD = 24;

    function setScrollArrowHidden(button, hidden) {
        if(!button) {
            return;
        }
        button.classList.toggle("category-browser-btn--hidden", hidden);
        if(hidden) {
            button.setAttribute("aria-hidden", "true");
            button.setAttribute("tabindex", "-1");
            if(document.activeElement === button) {
                button.blur();
            }
        } else {
            button.removeAttribute("aria-hidden");
            button.removeAttribute("tabindex");
        }
    }

    function updateScrollArrowButtons() {
        let y = window.scrollY || window.pageYOffset || 0;
        setScrollArrowHidden(document.getElementById("category-browser-top"), y <= SCROLL_ARROW_THRESHOLD);

        let target = activePlaylistScrollTarget();
        let atPlaylist = true;
        if(target) {
            // Near the page bottom the browser can't reach the target, so compare with where it actually lands
            let maxScroll = Math.max(document.documentElement.scrollHeight - window.innerHeight, 0);
            atPlaylist = Math.abs(y - Math.min(target.top, maxScroll)) <= SCROLL_ARROW_THRESHOLD;
        }
        setScrollArrowHidden(document.getElementById("category-browser-scroll"), atPlaylist);
    }

    let scrollArrowFrame = null;
    function scheduleScrollArrowUpdate() {
        if(scrollArrowFrame) {
            return;
        }
        scrollArrowFrame = requestAnimationFrame(function() {
            scrollArrowFrame = null;
            updateScrollArrowButtons();
        });
    }

    window.addEventListener("scroll", scheduleScrollArrowUpdate, { passive: true });
    window.addEventListener("resize", scheduleScrollArrowUpdate, { passive: true });
    $(function() {
        updateScrollArrowButtons();
        // Panels opening, the favs iframe resizing, etc. move the playlist; recheck on any page size change
        if(window.ResizeObserver) {
            new ResizeObserver(scheduleScrollArrowUpdate).observe(document.body);
        }
    });

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
            recordOpenedPlaylist(window.playlistId);
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

            // The next playlist's name may override Mode; the user's own Mode stays untouched
            var nextName = $el && $el.length ? $el.text() : getPlaylistNameById(playlistId);
            var nameMode = rememberModeOverride(playlistId, nextName);
            var nextIsLatest = nameMode ? nameMode === "latest" : localStorage.getItem("YT__shuffle_mode") === "0";
            if(nextIsLatest && !params.has("playlistStart")) {
                params.set("playlistStart", 1);
            }
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
        
        // The active playlist may have moved (collection switch, filtering)
        if (typeof scheduleScrollArrowUpdate === 'function') {
            scheduleScrollArrowUpdate();
        }

        // Rebuild section nav after iframe resizes
        if (typeof window.parentSectionNav !== 'undefined') {
            setTimeout(function() {
                window.parentSectionNav.buildSections();
            }, 100);
        }
    }

    function isShuffleMode() {
        // let isShuffling = window.location.href.indexOf("playlistStart=")===-1;

        // A "(Shuffled)" / "(Chronological)" playlist name wins over the user's Mode preference
        var playlistNameMode = getActiveModeOverride();
        if(playlistNameMode) {
            return playlistNameMode === "shuffle";
        }

        // If first opening app, default is no shuffling
        if(localStorage.getItem("YT__shuffle_mode")===null)
        localStorage.getItem("YT__shuffle_mode")==="0";

        let isShuffling = localStorage.getItem("YT__shuffle_mode")==="1";
        console.log("isShuffleMode: ", isShuffling);
        return isShuffling;
    }

    // ---- Mode override from the playlist name ----
    // YT__shuffle_mode is the user's own Mode (the one they last picked by hand).
    // YT__mode_override is {playlistId, mode, manual} for the playlist that is open now.
    // A tagged playlist only writes the override, so leaving it restores the user's Mode.
    var MODE_OVERRIDE_KEY = "YT__mode_override";

    function getModeFromPlaylistName(name) {
        var text = String(name || "").toLowerCase();
        var shuffleAt = Math.max(text.lastIndexOf("(shuffled)"), text.lastIndexOf("(shuffle)"));
        var chronologicalAt = text.lastIndexOf("(chronological)");
        if(shuffleAt < 0 && chronologicalAt < 0) {
            return null;
        }
        // Both tags in one name: the last one written wins
        return chronologicalAt > shuffleAt ? "latest" : "shuffle";
    }

    function readModeOverride() {
        var saved = null;
        try {
            saved = JSON.parse(localStorage.getItem(MODE_OVERRIDE_KEY) || "null");
        } catch(e) {
            return null;
        }
        if(!saved || saved.playlistId !== window.playlistId || (saved.mode !== "shuffle" && saved.mode !== "latest")) {
            return null;
        }
        return saved;
    }

    function getActiveModeOverride() {
        var saved = readModeOverride();
        return saved && !saved.manual ? saved.mode : null;
    }

    function rememberModeOverride(playlistId, playlistName) {
        var mode = getModeFromPlaylistName(playlistName);
        if(mode) {
            localStorage.setItem(MODE_OVERRIDE_KEY, JSON.stringify({ playlistId: playlistId, mode: mode }));
        } else {
            localStorage.removeItem(MODE_OVERRIDE_KEY);
        }
        return mode;
    }

    function getPlaylistNameById(playlistId) {
        try {
            var $a = $("#favs-wrapper iframe").contents().find("a[data-playlist-id]").filter(function() {
                return $(this).attr("data-playlist-id") === playlistId;
            }).first();
            return $a.length ? $a.text() : null;
        } catch(e) {
            return null;
        }
    }

    // Called by favs.php after it renders a collection: catches direct links and renamed playlists.
    function syncPlaylistModeOverride() {
        var name = getPlaylistNameById(window.playlistId);
        if(name === null) {
            return; // playlist is not in this collection; leave things as they are
        }
        var mode = getModeFromPlaylistName(name);
        var saved = readModeOverride();
        if(mode && !(saved && saved.mode === mode)) {
            localStorage.setItem(MODE_OVERRIDE_KEY, JSON.stringify({ playlistId: window.playlistId, mode: mode }));
        } else if(!mode && saved) {
            localStorage.removeItem(MODE_OVERRIDE_KEY);
        }
        updatePlaybackModeSelect();
    }

    // A "(Chronological)" playlist starts at the newest video even without playlistStart in the URL
    if(getActiveModeOverride() === "latest" && !String(window.playlistStartIndex || "").length) {
        window.playlistStartIndex = "1";
    }

    function togglePlaybackModeInfo(event, forceOpen) {
        if(event) {
            event.stopPropagation();
        }
        var $btn = $("#playback-mode-info-btn");
        var $panel = $("#playback-mode-info");
        var open = typeof forceOpen === "boolean" ? forceOpen : $panel.prop("hidden");
        $panel.prop("hidden", !open);
        $btn.attr("aria-expanded", open ? "true" : "false");
        if(open) {
            $panel.trigger("focus");
        }
    }

    $(document).on("click", function(event) {
        if(!$(event.target).closest("#playback-mode-info, #playback-mode-info-btn").length && !$("#playback-mode-info").prop("hidden")) {
            togglePlaybackModeInfo(null, false);
        }
    });

    $(document).on("keydown", function(event) {
        if(event.key === "Escape" && !$("#playback-mode-info").prop("hidden")) {
            togglePlaybackModeInfo(null, false);
            $("#playback-mode-info-btn").trigger("focus");
        }
    });

    $(()=>{
        syncVideoStartModeButtons();
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

        initBrightnessControl();
        initWatchTime();
        initWatchHistory();
        initPlayerVideoActionTooltips();
        initAppMetaCarousel();

    }); // on doc ready

    const WATCH_TIME_STORAGE_KEY = "YT__watch-time-today";
    const WATCH_TIME_TICK_MS = 1000;
    const WATCH_TIME_MAX_PROGRESS_MS = 2500;

    window.ytWatchTime = {
        sessionMs: 0,
        todayMs: 0,
        todayKey: "",
        intervalId: null,
        lastCountedVideoTime: null,
        lastCountedVideoId: null
    };

    function getWatchTimeTodayKey() {
        let d = new Date();
        let month = String(d.getMonth() + 1).padStart(2, "0");
        let day = String(d.getDate()).padStart(2, "0");
        return d.getFullYear() + "-" + month + "-" + day;
    }

    function formatWatchTime(ms) {
        let totalSeconds = Math.max(0, Math.floor(ms / 1000));
        let hours = Math.floor(totalSeconds / 3600);
        let minutes = Math.floor((totalSeconds % 3600) / 60);
        let seconds = totalSeconds % 60;

        if(hours > 0) {
            return hours + "h " + minutes + "m";
        }
        if(minutes > 0) {
            return minutes + "m " + seconds + "s";
        }
        return seconds + "s";
    }

    function ensureWatchTimeTodayKey() {
        let todayKey = getWatchTimeTodayKey();
        if(window.ytWatchTime.todayKey !== todayKey) {
            window.ytWatchTime.todayKey = todayKey;
            window.ytWatchTime.todayMs = 0;
        }
    }

    function loadTodayWatchTime() {
        ensureWatchTimeTodayKey();
        let saved = null;

        try {
            saved = JSON.parse(localStorage.getItem(WATCH_TIME_STORAGE_KEY) || "null");
        } catch(e) {
            saved = null;
        }

        if(saved && saved.date === window.ytWatchTime.todayKey && typeof saved.ms === "number" && isFinite(saved.ms)) {
            window.ytWatchTime.todayMs = Math.max(0, saved.ms);
        } else {
            window.ytWatchTime.todayMs = 0;
        }
    }

    function saveTodayWatchTime() {
        ensureWatchTimeTodayKey();
        localStorage.setItem(WATCH_TIME_STORAGE_KEY, JSON.stringify({
            date: window.ytWatchTime.todayKey,
            ms: Math.max(0, Math.round(window.ytWatchTime.todayMs))
        }));
    }

    function renderWatchTimeDisplay() {
        $("#watch-time-today-value").text(formatWatchTime(window.ytWatchTime.todayMs));
        $("#watch-time-session-value").text(formatWatchTime(window.ytWatchTime.sessionMs));
    }

    function shouldCountWatchTime() {
        if(!$("#player-fallback").hasClass("d-none")) {
            return false;
        }

        if(typeof YT === "undefined" || !window.player1) {
            return false;
        }

        let state;
        try {
            state = window.player1.getPlayerState();
        } catch(e) {
            return false;
        }

        if(state !== YT.PlayerState.PLAYING) {
            return false;
        }

        let snapshot = getPlayerSnapshot();
        if(hasFatalPlayerSnapshot(snapshot, Date.now())) {
            return false;
        }

        return true;
    }

    function resetWatchTimeProgressMarkers() {
        window.ytWatchTime.lastCountedVideoTime = null;
        window.ytWatchTime.lastCountedVideoId = null;
    }

    function tickWatchTime() {
        ensureWatchTimeTodayKey();

        if(!shouldCountWatchTime()) {
            resetWatchTimeProgressMarkers();
            renderWatchTimeDisplay();
            return;
        }

        let currentTime;
        let videoId = "";

        try {
            currentTime = window.player1.getCurrentTime();
            videoId = getVideoId();
        } catch(e) {
            resetWatchTimeProgressMarkers();
            return;
        }

        if(typeof currentTime !== "number" || !isFinite(currentTime)) {
            resetWatchTimeProgressMarkers();
            return;
        }

        if(
            window.ytWatchTime.lastCountedVideoId === videoId &&
            window.ytWatchTime.lastCountedVideoTime !== null &&
            currentTime > window.ytWatchTime.lastCountedVideoTime
        ) {
            let deltaMs = (currentTime - window.ytWatchTime.lastCountedVideoTime) * 1000;
            if(deltaMs > 0 && deltaMs <= WATCH_TIME_MAX_PROGRESS_MS) {
                window.ytWatchTime.sessionMs += deltaMs;
                window.ytWatchTime.todayMs += deltaMs;
                saveTodayWatchTime();
                recordPlaylistWatchMinutes(window.playlistId, deltaMs);
            }
        }

        window.ytWatchTime.lastCountedVideoTime = currentTime;
        window.ytWatchTime.lastCountedVideoId = videoId;
        renderWatchTimeDisplay();
    }

    function resetTodayWatchTime() {
        window.ytWatchTime.todayMs = 0;
        resetWatchTimeProgressMarkers();
        saveTodayWatchTime();
        renderWatchTimeDisplay();
    }

    function resetSessionWatchTime() {
        window.ytWatchTime.sessionMs = 0;
        resetWatchTimeProgressMarkers();
        renderWatchTimeDisplay();
    }

    function resetAllWatchTime() {
        window.ytWatchTime.sessionMs = 0;
        window.ytWatchTime.todayMs = 0;
        resetWatchTimeProgressMarkers();
        saveTodayWatchTime();
        renderWatchTimeDisplay();
    }

    function handleWatchTimeClick(event, scope) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if(scope === "today") {
            if(!window.confirm("Reset today's watch time?")) {
                return;
            }
            resetTodayWatchTime();
            return;
        }

        if(scope === "session") {
            if(!window.confirm("Reset this session's watch time?")) {
                return;
            }
            resetSessionWatchTime();
            return;
        }

        if(scope === "both") {
            if(!window.confirm("Reset watch time for today and this session?")) {
                return;
            }
            resetAllWatchTime();
        }
    }

    function initWatchTime() {
        if(!$("#watch-time-display").length) {
            return;
        }

        loadTodayWatchTime();
        renderWatchTimeDisplay();

        if(window.ytWatchTime.intervalId !== null) {
            clearInterval(window.ytWatchTime.intervalId);
        }

        window.ytWatchTime.intervalId = setInterval(tickWatchTime, WATCH_TIME_TICK_MS);

        $(".watch-time-stat").on("keydown", function(event) {
            if(event.key === "Enter" || event.key === " ") {
                let scope = $(this).hasClass("watch-time-stat--today") ? "today" : "session";
                handleWatchTimeClick(event, scope);
            }
        });
    }

    const WATCH_HISTORY_STORAGE_KEY = "YT__watch-history";
    const WATCH_HISTORY_MAX_ENTRIES = 1000;
    let watchHistorySort = { key: "", dir: "desc" };

    function loadWatchHistory() {
        try {
            let parsed = JSON.parse(localStorage.getItem(WATCH_HISTORY_STORAGE_KEY) || "null");
            if(!Array.isArray(parsed)) {
                return [];
            }
            return parsed.filter(function(entry) {
                return entry && typeof entry.playlistId === "string" && entry.playlistId.length;
            });
        } catch(e) {
            return [];
        }
    }

    function saveWatchHistory(entries) {
        localStorage.setItem(
            WATCH_HISTORY_STORAGE_KEY,
            JSON.stringify(entries.slice(0, WATCH_HISTORY_MAX_ENTRIES))
        );
    }

    function currentFavsBundle() {
        try {
            let iframe = $("#favs-wrapper iframe")[0];
            if(iframe && iframe.contentWindow) {
                let match = String(iframe.contentWindow.location.href).match(/[?&]favs=([^&]+)/);
                if(match) {
                    return decodeURIComponent(match[1]).replace(/\.php$/i, "");
                }
            }
        } catch(e) {}
        return new URLSearchParams(window.location.search).get("favs") || "Default";
    }

    function playlistTitleForId(playlistId) {
        try {
            let $link = $("#favs-wrapper iframe").contents().find("a[data-playlist-id]").filter(function() {
                return $(this).attr("data-playlist-id") === playlistId;
            }).first();
            if($link.length) {
                let text = $link.text().replace(/\s+/g, " ").trim();
                if(text) {
                    return text;
                }
            }
        } catch(e) {}
        return "";
    }

    function seedWatchHistoryFromLastOpened() {
        if(localStorage.getItem(WATCH_HISTORY_STORAGE_KEY) !== null) {
            return;
        }

        let ids = Array.isArray(window.arrLastOpened) ? window.arrLastOpened.slice() : [];
        let seeded = [];
        for(let i = ids.length - 1; i >= 0; i--) {
            let playlistId = ids[i];
            if(!playlistId || seeded.some(function(entry) { return entry.playlistId === playlistId; })) {
                continue;
            }
            seeded.push({
                playlistId: playlistId,
                title: "",
                favs: "",
                date: "",
                ms: 0,
                updatedAt: 0
            });
        }
        saveWatchHistory(seeded);
    }

    function markPlaylistOpened(playlistId) {
        if(!playlistId) {
            return;
        }

        let list = Array.isArray(window.arrLastOpened) ? window.arrLastOpened.slice() : [];
        if(list.length && list[list.length - 1] === playlistId) {
            window.arrLastOpened = list;
            return;
        }

        list = list.filter(function(id) {
            return id !== playlistId;
        });
        list.push(playlistId);
        window.arrLastOpened = list;
        localStorage.setItem("YT__last-opened", JSON.stringify(list));
        if(typeof renderCrossedOutPlaylists === "function") {
            renderCrossedOutPlaylists(list);
        }
    }

    function upsertWatchHistory(playlistId, deltaMs) {
        if(!playlistId) {
            return null;
        }

        let today = getWatchTimeTodayKey();
        let entries = loadWatchHistory().filter(function(entry) {
            return !(entry.playlistId === playlistId && !entry.date);
        });
        let index = -1;
        for(let i = 0; i < entries.length; i++) {
            if(entries[i].playlistId === playlistId && entries[i].date === today) {
                index = i;
                break;
            }
        }

        let title = playlistTitleForId(playlistId);
        let favs = currentFavsBundle();
        let now = Date.now();
        let entry;
        let moved = false;

        if(index === -1) {
            entry = {
                playlistId: playlistId,
                title: title,
                favs: favs,
                date: today,
                ms: Math.max(0, Math.round(deltaMs || 0)),
                updatedAt: now
            };
            entries.unshift(entry);
            moved = true;
        } else {
            entry = entries[index];
            entry.ms = Math.max(0, Math.round((Number(entry.ms) || 0) + (deltaMs || 0)));
            entry.updatedAt = now;
            if(title) {
                entry.title = title;
            }
            if(favs) {
                entry.favs = favs;
            }
            if(index !== 0) {
                entries.splice(index, 1);
                entries.unshift(entry);
                moved = true;
            }
        }

        saveWatchHistory(entries);
        return { entry: entry, moved: moved };
    }

    function formatHistoryDate(dateKey) {
        if(!dateKey) {
            return "—";
        }
        let parts = String(dateKey).split("-");
        if(parts.length !== 3) {
            return dateKey;
        }
        let year = parts[0];
        let month = parts[1].padStart(2, "0");
        let day = parts[2].padStart(2, "0");
        if(!/^\d{4}$/.test(year) || !/^\d{2}$/.test(month) || !/^\d{2}$/.test(day)) {
            return dateKey;
        }
        return year + "." + month + "." + day;
    }

    function formatHistoryMinutes(ms, dateKey) {
        if(!dateKey && !(ms > 0)) {
            return "—";
        }
        let totalSeconds = Math.max(0, Math.floor((Number(ms) || 0) / 1000));
        let hours = Math.floor(totalSeconds / 3600);
        let minutes = Math.floor((totalSeconds % 3600) / 60);
        if(hours > 0) {
            return hours + "h " + minutes + "m";
        }
        if(minutes > 0) {
            return minutes + "m";
        }
        if(totalSeconds > 0) {
            return "<1m";
        }
        return "0m";
    }

    function isWatchHistoryOpen() {
        return $("#watch-history-root").hasClass("is-open");
    }

    function appendWrappableText($el, title) {
        $el.empty();
        String(title || "").split(/(\s+|[/\[\]])/).forEach(function(part) {
            if(part === "") {
                return;
            }
            $el.append(document.createTextNode(part));
            if(part === "/" || part === "]" || part === "[") {
                $el.append(document.createElement("wbr"));
            }
        });
    }

    function sortWatchHistoryEntries(entries) {
        if(watchHistorySort.key !== "date" && watchHistorySort.key !== "minutes") {
            return entries;
        }
        let dir = watchHistorySort.dir === "asc" ? 1 : -1;
        return entries.slice().sort(function(a, b) {
            if(watchHistorySort.key === "date") {
                let ad = a.date || "";
                let bd = b.date || "";
                if(ad !== bd) {
                    return ad < bd ? -dir : dir;
                }
            } else {
                let am = Number(a.ms) || 0;
                let bm = Number(b.ms) || 0;
                if(am !== bm) {
                    return am < bm ? -dir : dir;
                }
            }
            return 0;
        });
    }

    function syncWatchHistorySortHeaders() {
        $(".watch-history-sort").each(function() {
            let key = $(this).attr("data-sort");
            let active = watchHistorySort.key === key;
            let $th = $(this).closest("th");
            $th.attr("aria-sort", active ? (watchHistorySort.dir === "asc" ? "ascending" : "descending") : "none");
            let icon = "fa-sort";
            if(active) {
                icon = watchHistorySort.dir === "asc" ? "fa-sort-up" : "fa-sort-down";
            }
            $(this).find("i").attr("class", "fa " + icon);
        });
    }

    function reorderWatchHistoryRows() {
        if(watchHistorySort.key !== "date" && watchHistorySort.key !== "minutes") {
            return;
        }
        let $rows = $("#watch-history-rows");
        let dir = watchHistorySort.dir === "asc" ? 1 : -1;
        let rows = $rows.children(".watch-history-row").get();
        rows.sort(function(a, b) {
            if(watchHistorySort.key === "date") {
                let ad = a.getAttribute("data-date") || "";
                let bd = b.getAttribute("data-date") || "";
                if(ad !== bd) {
                    return ad < bd ? -dir : dir;
                }
            } else {
                let am = Number(a.getAttribute("data-ms")) || 0;
                let bm = Number(b.getAttribute("data-ms")) || 0;
                if(am !== bm) {
                    return am < bm ? -dir : dir;
                }
            }
            return 0;
        });
        rows.forEach(function(row) {
            $rows.append(row);
        });
    }

    function deleteWatchHistoryEntry(playlistId, dateKey) {
        let removed = false;
        let entries = loadWatchHistory().filter(function(entry) {
            if(!removed && entry.playlistId === playlistId && (entry.date || "") === (dateKey || "")) {
                removed = true;
                return false;
            }
            return true;
        });
        saveWatchHistory(entries);
        renderWatchHistory();
    }

    function renderWatchHistory() {
        let entries = loadWatchHistory();
        let titlesChanged = false;
        entries.forEach(function(entry) {
            if(entry.title) {
                return;
            }
            let title = playlistTitleForId(entry.playlistId);
            if(title) {
                entry.title = title;
                titlesChanged = true;
            }
        });
        if(titlesChanged) {
            saveWatchHistory(entries);
        }

        let $rows = $("#watch-history-rows");
        let $empty = $("#watch-history-empty");
        let $table = $("#watch-history-table");
        if(!$rows.length) {
            return;
        }

        entries = sortWatchHistoryEntries(entries);
        $rows.empty();
        syncWatchHistorySortHeaders();
        if(!entries.length) {
            $empty.removeAttr("hidden");
            $table.attr("hidden", true);
            return;
        }

        $empty.attr("hidden", true);
        $table.removeAttr("hidden");
        entries.forEach(function(entry) {
            let title = entry.title || entry.playlistId;
            let $tr = $("<tr>")
                .addClass("watch-history-row")
                .attr("data-playlist-id", entry.playlistId)
                .attr("data-date", entry.date || "")
                .attr("data-ms", String(Number(entry.ms) || 0));
            if(entry.favs) {
                $tr.attr("data-favs", entry.favs);
            }

            let $button = $("<button>", {
                type: "button",
                class: "watch-history-playlist",
                title: "Open this playlist"
            });
            appendWrappableText($button, title);
            $("<td>").append($button).appendTo($tr);

            let $time = $("<time>", { class: "watch-history-meta watch-history-date" }).text(formatHistoryDate(entry.date));
            if(entry.date) {
                $time.attr("datetime", entry.date);
            }
            $("<td>").append($time).appendTo($tr);

            $("<td>").addClass("watch-history-minutes-cell").append(
                $("<span>", { class: "watch-history-meta watch-history-minutes" }).text(formatHistoryMinutes(entry.ms, entry.date))
            ).appendTo($tr);

            $("<td>").addClass("watch-history-delete-cell").append(
                $("<button>", {
                    type: "button",
                    class: "watch-history-delete",
                    title: "Delete this entry",
                    "aria-label": "Delete " + title
                }).append($("<i>", { class: "fa fa-trash-alt", "aria-hidden": "true" }))
            ).appendTo($tr);
            $rows.append($tr);
        });
    }

    function syncOpenHistoryRow(result) {
        if(!result || !isWatchHistoryOpen()) {
            return;
        }
        if(!watchHistorySort.key && result.moved) {
            renderWatchHistory();
            return;
        }

        let entry = result.entry;
        let $row = $("#watch-history-rows .watch-history-row").filter(function() {
            return $(this).attr("data-playlist-id") === entry.playlistId && $(this).attr("data-date") === entry.date;
        }).first();
        if(!$row.length) {
            renderWatchHistory();
            return;
        }
        $row.attr("data-ms", String(Number(entry.ms) || 0));
        $row.find(".watch-history-minutes").text(formatHistoryMinutes(entry.ms, entry.date));
        if(entry.title) {
            appendWrappableText($row.find(".watch-history-playlist"), entry.title);
        }
        if(watchHistorySort.key === "minutes") {
            reorderWatchHistoryRows();
        }
    }

    function recordOpenedPlaylist(playlistId) {
        if(!playlistId) {
            return;
        }
        markPlaylistOpened(playlistId);
        syncOpenHistoryRow(upsertWatchHistory(playlistId, 0));
    }

    function recordPlaylistWatchMinutes(playlistId, deltaMs) {
        if(!playlistId || !(deltaMs > 0)) {
            return;
        }
        markPlaylistOpened(playlistId);
        syncOpenHistoryRow(upsertWatchHistory(playlistId, deltaMs));
    }

    function closeWatchHistoryMenu() {
        $("#watch-history-menu").attr("hidden", true);
        $("#watch-history-menu-toggle").attr("aria-expanded", "false");
    }

    function toggleWatchHistoryMenu(event) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }
        let $menu = $("#watch-history-menu");
        if($menu.is("[hidden]")) {
            $menu.removeAttr("hidden");
            $("#watch-history-menu-toggle").attr("aria-expanded", "true");
            return;
        }
        closeWatchHistoryMenu();
    }

    function openWatchHistoryPanel() {
        closeWatchHistoryMenu();
        renderWatchHistory();
        let root = document.getElementById("watch-history-root");
        if(!root) {
            return;
        }
        root.setAttribute("aria-hidden", "false");
        root.classList.add("is-closing");
        window.requestAnimationFrame(function() {
            root.classList.add("is-open");
        });
        $("#watch-history-open").attr("aria-expanded", "true");
        document.body.style.overflow = "hidden";
        $("#watch-history-close").trigger("focus");
    }

    function closeWatchHistoryPanel() {
        let root = document.getElementById("watch-history-root");
        if(!root || !root.classList.contains("is-open")) {
            return;
        }
        root.classList.remove("is-open");
        $("#watch-history-open").attr("aria-expanded", "false");
        document.body.style.overflow = "";

        let panel = root.querySelector(".watch-history-panel");
        let finished = false;
        let done = function() {
            if(finished || root.classList.contains("is-open")) {
                return;
            }
            finished = true;
            root.classList.remove("is-closing");
            root.setAttribute("aria-hidden", "true");
        };
        if(panel) {
            panel.addEventListener("transitionend", done, { once: true });
        }
        window.setTimeout(done, 360);
        $("#watch-history-open").trigger("focus");
    }

    function clearWatchHistory() {
        window.arrLastOpened = [];
        localStorage.setItem("YT__last-opened", "[]");
        localStorage.setItem(WATCH_HISTORY_STORAGE_KEY, "[]");
        $("#favs-wrapper iframe").contents().find(".crossed-out").removeClass("crossed-out");
        closeWatchHistoryMenu();
        renderWatchHistory();
    }

    function openPlaylistFromHistory(playlistId, favs) {
        if(!playlistId) {
            return;
        }
        let $link = $("#favs-wrapper iframe").contents().find("a[data-playlist-id]").filter(function() {
            return $(this).attr("data-playlist-id") === playlistId;
        }).first();
        if($link.length) {
            closeWatchHistoryPanel();
            $link.trigger("click");
            return;
        }

        let params = new URLSearchParams(window.location.search);
        params.set("playlistId", playlistId);
        params.delete("loop-video-id");
        if(favs) {
            params.set("favs", favs);
        }
        window.location.search = params.toString();
    }

    function initWatchHistory() {
        seedWatchHistoryFromLastOpened();

        $("#watch-history-open").on("click", function() {
            openWatchHistoryPanel();
        });
        $("#watch-history-menu-toggle").on("click", toggleWatchHistoryMenu);
        $("#watch-history-clear").on("click", function(event) {
            event.preventDefault();
            event.stopPropagation();
            if(!window.confirm("Clear history?")) {
                return;
            }
            clearWatchHistory();
        });
        $("#watch-history-backdrop, #watch-history-close").on("click", function() {
            closeWatchHistoryPanel();
        });
        $("#watch-history-advanced").on("change", function() {
            $("#watch-history-table, #watch-history-footer").toggleClass("is-advanced", this.checked);
        });
        $("#watch-history-clear-all").on("click", function() {
            if(!$("#watch-history-footer").hasClass("is-advanced")) {
                return;
            }
            if(!window.confirm("Clear all recently watched?")) {
                return;
            }
            clearWatchHistory();
        });
        $("#watch-history-table").on("click", ".watch-history-sort", function() {
            let key = $(this).attr("data-sort");
            if(watchHistorySort.key === key) {
                watchHistorySort.dir = watchHistorySort.dir === "desc" ? "asc" : "desc";
            } else {
                watchHistorySort.key = key;
                watchHistorySort.dir = "desc";
            }
            renderWatchHistory();
        });
        $("#watch-history-rows").on("click", ".watch-history-playlist", function() {
            let $row = $(this).closest("tr");
            openPlaylistFromHistory($row.attr("data-playlist-id"), $row.attr("data-favs") || "");
        });
        $("#watch-history-rows").on("click", ".watch-history-delete", function(event) {
            event.preventDefault();
            event.stopPropagation();
            if(!$("#watch-history-table").hasClass("is-advanced")) {
                return;
            }
            let $row = $(this).closest("tr");
            let title = $row.find(".watch-history-playlist").text().replace(/\s+/g, " ").trim();
            let dateLabel = $row.find(".watch-history-date").text().trim();
            let message = "Delete " + (title ? "\"" + title + "\"" : "this entry");
            if(dateLabel && dateLabel !== "—") {
                message += " from " + dateLabel;
            }
            message += "?";
            if(!window.confirm(message)) {
                return;
            }
            deleteWatchHistoryEntry($row.attr("data-playlist-id"), $row.attr("data-date") || "");
        });
        $(document).on("click", function(event) {
            if(!$(event.target).closest(".watch-history-split").length) {
                closeWatchHistoryMenu();
            }
        });
        $(document).on("keydown", function(event) {
            if(event.key !== "Escape") {
                return;
            }
            if(!$("#watch-history-menu").is("[hidden]")) {
                closeWatchHistoryMenu();
                $("#watch-history-menu-toggle").trigger("focus");
                return;
            }
            if(isWatchHistoryOpen()) {
                closeWatchHistoryPanel();
            }
        });
    }

    const BRIGHTNESS_MIN = 20;
    const BRIGHTNESS_MAX = 100;
    const BRIGHTNESS_STORAGE_KEY = "YT__page-brightness";

    function applyPageBrightness(percent) {
        const clamped = Math.max(BRIGHTNESS_MIN, Math.min(BRIGHTNESS_MAX, percent));
        const brightness = clamped / 100;
        const $root = $("html");
        const $control = $("#player-options");

        if(brightness >= 1) {
            $root.css("filter", "");
            $control.css("filter", "");
        } else {
            $root.css("filter", "brightness(" + brightness + ")");
            $control.css("filter", "brightness(" + (1 / brightness) + ")");
        }

        $("#brightness-slider").val(clamped).attr("aria-valuenow", clamped);
        $("#brightness-control .brightness-value").text(clamped + "%");
        localStorage.setItem(BRIGHTNESS_STORAGE_KEY, String(clamped));
        updateBrightnessPresetState(clamped);
    }

    function updateBrightnessPresetState(percent) {
        $(".brightness-preset").each(function() {
            let preset = parseInt($(this).data("brightness"), 10);
            $(this).toggleClass("brightness-preset--active", preset === percent);
        });
    }

    function initBrightnessControl() {
        const $slider = $("#brightness-slider");
        if(!$slider.length) {
            return;
        }

        let saved = parseInt(localStorage.getItem(BRIGHTNESS_STORAGE_KEY), 10);
        if(isNaN(saved)) {
            saved = BRIGHTNESS_MAX;
        }

        applyPageBrightness(saved);

        $slider.on("input change", function() {
            applyPageBrightness(parseInt(this.value, 10));
        });

        $(".brightness-track-wrap").on("click", function(event) {
            if(event.target === $slider[0]) {
                return;
            }

            const rect = this.getBoundingClientRect();
            const ratio = (event.clientX - rect.left) / rect.width;
            const percent = BRIGHTNESS_MIN + ratio * (BRIGHTNESS_MAX - BRIGHTNESS_MIN);
            applyPageBrightness(Math.round(percent));
        });

        $(".brightness-preset").on("click", function() {
            applyPageBrightness(parseInt($(this).data("brightness"), 10));
        });
    }

    function adjacentPlaylistAnchor($fromLink, direction) {
        var $doc = $("#favs-wrapper iframe").contents();
        var $lis = $doc.find(".playlists-target > li");
        var start = $lis.index($fromLink.closest("li"));
        var step = direction < 0 ? -1 : 1;

        function linkAt(index) {
            return $lis.eq(index).children("a[data-playlist-id], a[data-open]").first();
        }

        for (var i = start + step; i >= 0 && i < $lis.length; i += step) {
            var $link = linkAt(i);
            if ($link.length) return $link;
        }

        for (var j = step > 0 ? 0 : $lis.length - 1; j >= 0 && j < $lis.length; j += step) {
            var $wrap = linkAt(j);
            if ($wrap.length) return $wrap;
        }

        return $();
    }

    function openNextPlaylist() {
        var searchParams = (new URLSearchParams(window.location.search));
        var nextPlaylistId = searchParams.get("playlistId");

        if(nextPlaylistId!==null) {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id='${nextPlaylistId}']`);
            var $b = adjacentPlaylistAnchor($a, 1);

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
            var $b = adjacentPlaylistAnchor($a, -1);

            $b.click();
            // debugger;
        } else {
            var $a = $("#favs-wrapper iframe").contents().find(`[data-playlist-id]`).first();
            searchParams.set("playlistId", $a.data("playlist-id"));
            var urlTrailing = searchParams.toString();
            window.location.search = urlTrailing;
        }
    } // openPreviousPlaylist
    function syncVideoStartModeButtons() {
        updatePlaybackModeSelect();
    }

    function updatePlaybackModeSelect() {
        let $select = $("#playback-mode-select");
        if(!$select.length) {
            return;
        }

        let isShuffling = isShuffleMode();
        $select.val(isShuffling ? "shuffle" : "latest");
        let playlistNameMode = getActiveModeOverride();
        $("#playback-mode-control")
            .toggleClass("playback-mode-control--shuffle", isShuffling)
            .toggleClass("playback-mode-control--chronological", !isShuffling)
            .toggleClass("playback-mode-control--overridden", !!playlistNameMode);
        $("#playback-mode-override-note")
            .prop("hidden", !playlistNameMode)
            .attr("title", playlistNameMode === "shuffle"
                ? "This playlist's name has (Shuffled), so it plays shuffled. Your own Mode comes back on the next playlist."
                : "This playlist's name has (Chronological), so it plays latest first. Your own Mode comes back on the next playlist.");
    }

    function setShuffleMode(wantChronological) {
        if(wantChronological) {
            localStorage.setItem("YT__shuffle_mode", 0);
        } else {
            localStorage.setItem("YT__shuffle_mode", 1);
        }
        syncVideoStartModeButtons();
    }

    function handlePlaybackModeChange() {
        let mode = $("#playback-mode-select").val();
        // Picking a Mode by hand becomes the user's Mode and beats the playlist name until they leave this playlist
        let saved = readModeOverride();
        if(saved) {
            saved.manual = true;
            localStorage.setItem(MODE_OVERRIDE_KEY, JSON.stringify(saved));
        }
        if(mode === "shuffle") {
            setShuffleMode(false);
            urlChange.start("RANDOM");
        } else {
            setShuffleMode(true);
            urlChange.start(1);
        }
    }
    
    function syncLoopBtnState($btn) {
        let $target = $btn && $btn.length ? $btn : $(".loop-btn");
        let isActive = $target.hasClass("active");
        let label = isActive ? "Exit loop" : "Loop video";
        let tip = isActive ? "Stop looping this video" : "Loop the current video";
        $target
            .attr("aria-pressed", isActive ? "true" : "false")
            .attr("aria-label", label)
            .attr("title", tip)
            .attr("data-original-title", tip)
            .find(".player-video-action-label")
            .text(label);
    }

    function initPlayerVideoActionTooltips() {
        let $actions = $(".player-video-action");
        if(!$actions.length || typeof $actions.tooltip !== "function") {
            return;
        }

        $actions.tooltip({
            container: "body",
            placement: "top",
            trigger: "hover focus",
            delay: { show: 120, hide: 80 }
        });
    }

    function handleLoopBtn($this) {
        if($this.hasClass("active")) {
            $this.removeClass("active");
            syncLoopBtnState($this);
            window.history.back();
        } else {
            $this.addClass("active");
            syncLoopBtnState($this);
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
        let $label = $indicator.find(".playlist-position-label-text");
        if(!$indicator.length || !$label.length) {
            return;
        }

        if(isVisible) {
            $label.text(text);
            $indicator.removeClass("d-none");
        } else {
            closePlaylistVideosPanel();
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

    // Optional YouTube Data API titles for the playlist list (same YOUTUBE_API_KEY as upload date).
    // When the key is missing/invalid, the list still opens with "Video N" placeholders.
    window.ytPlaylistVideos = {
        requestId: 0,
        titlesById: {},
        enabled: null,
        loading: false
    };

    function disablePlaylistVideoTitles(statusText) {
        window.ytPlaylistVideos.enabled = false;
        window.ytPlaylistVideos.loading = false;
        setPlaylistVideosStatus(
            statusText || "Titles unavailable without a valid YouTube API key",
            true
        );
    }

    function closePlaylistVideosPanel() {
        let $panel = $("#playlist-videos-panel");
        if(!$panel.length) {
            return;
        }

        $panel.attr("hidden", true);
        $("#playlist-position-label-btn").attr("aria-expanded", "false");
    }

    function isPlaylistVideosPanelOpen() {
        let $panel = $("#playlist-videos-panel");
        return $panel.length > 0 && !$panel.is("[hidden]");
    }

    function setPlaylistVideosStatus(text, isVisible) {
        let $status = $("#playlist-videos-status");
        if(!$status.length) {
            return;
        }

        if(isVisible && text) {
            $status.text(text).removeClass("d-none");
        } else {
            $status.text("").addClass("d-none");
        }
    }

    function getCachedPlaylistVideoTitle(videoId) {
        if(!videoId) {
            return "";
        }

        let cached = window.ytPlaylistVideos.titlesById[videoId];
        return typeof cached === "string" ? cached : "";
    }

    function rememberPlaylistVideoTitle(videoId, title) {
        if(!videoId || typeof title !== "string" || title === "") {
            return;
        }

        window.ytPlaylistVideos.titlesById[videoId] = title;
    }

    function seedCurrentPlaylistVideoTitle(details) {
        if(!details || !details.videoId || !window.player1) {
            return;
        }

        try {
            let data = window.player1.getVideoData();
            if(data && typeof data.title === "string" && data.title !== "") {
                rememberPlaylistVideoTitle(details.videoId, data.title);
            }
        } catch(e) {
            // Player may not expose title yet.
        }
    }

    function scrollPlaylistVideosCurrentIntoView() {
        let $list = $("#playlist-videos-list");
        let $current = $list.find(".playlist-videos-item.is-current").first();
        if(!$list.length || !$current.length) {
            return;
        }

        let listEl = $list[0];
        let currentEl = $current[0];
        let top = currentEl.offsetTop - (listEl.clientHeight / 2) + (currentEl.clientHeight / 2);
        listEl.scrollTop = Math.max(top, 0);
    }

    function highlightPlaylistVideosCurrent(playlistIndex) {
        let $items = $("#playlist-videos-list .playlist-videos-item");
        if(!$items.length) {
            return;
        }

        $items.each(function() {
            let index = parseInt($(this).attr("data-index"), 10);
            let isCurrent = index === playlistIndex;
            $(this).toggleClass("is-current", isCurrent);
            if(isCurrent) {
                $(this).attr("aria-current", "true");
            } else {
                $(this).removeAttr("aria-current");
            }
        });
    }

    function renderPlaylistVideosList(details) {
        let $list = $("#playlist-videos-list");
        if(!$list.length || !details) {
            return;
        }

        $list.empty();

        details.playlist.forEach(function(videoId, index) {
            let title = getCachedPlaylistVideoTitle(videoId);
            if(title === "") {
                title = "Video " + (index + 1);
            }

            let $btn = $("<button/>", {
                type: "button",
                class: "playlist-videos-item" + (index === details.playlistIndex ? " is-current" : ""),
                "data-index": String(index),
                "data-video-id": videoId
            });
            if(index === details.playlistIndex) {
                $btn.attr("aria-current", "true");
            }

            $btn.append($("<span/>", {
                class: "playlist-videos-item-index",
                text: String(index + 1)
            }));
            $btn.append($("<span/>", {
                class: "playlist-videos-item-title",
                text: title
            }));
            $btn.on("click", function(event) {
                event.preventDefault();
                event.stopPropagation();
                playPlaylistVideoAt(index);
            });

            $list.append($btn);
        });

        scrollPlaylistVideosCurrentIntoView();
    }

    function applyPlaylistVideoTitles(titlesById) {
        if(!titlesById || typeof titlesById !== "object") {
            return;
        }

        Object.keys(titlesById).forEach(function(videoId) {
            rememberPlaylistVideoTitle(videoId, titlesById[videoId]);
        });

        $("#playlist-videos-list .playlist-videos-item").each(function() {
            let videoId = $(this).attr("data-video-id");
            let title = getCachedPlaylistVideoTitle(videoId);
            if(title !== "") {
                $(this).find(".playlist-videos-item-title").text(title);
            }
        });
    }

    function fetchPlaylistVideoTitleChunk(videoIds) {
        return $.ajax({
            url: "video-meta.php",
            data: { ids: videoIds.join(",") },
            dataType: "json",
            timeout: 12000
        }).then(function(data) {
            if(!data || data.enabled === false) {
                disablePlaylistVideoTitles();
                return {};
            }

            if(data.error) {
                // Invalid/restricted key or upstream rejection: keep the list usable without titles.
                disablePlaylistVideoTitles();
                return {};
            }

            window.ytPlaylistVideos.enabled = true;
            let titlesById = {};
            let videos = Array.isArray(data.videos) ? data.videos : [];
            videos.forEach(function(video) {
                if(
                    video &&
                    typeof video.videoId === "string" &&
                    typeof video.title === "string" &&
                    video.title !== ""
                ) {
                    titlesById[video.videoId] = video.title;
                }
            });
            return titlesById;
        }, function(xhr) {
            let data = xhr && xhr.responseJSON ? xhr.responseJSON : null;
            if(
                (data && (data.enabled === false || data.error)) ||
                (xhr && (xhr.status === 401 || xhr.status === 403))
            ) {
                disablePlaylistVideoTitles();
            }
            // Transient failures: leave enabled unknown so a later open can retry.
            return {};
        });
    }

    function loadPlaylistVideoTitles(details) {
        if(!details) {
            return;
        }

        seedCurrentPlaylistVideoTitle(details);

        if(
            window.ytPlaylistVideos.enabled === false ||
            (window.ytUploadDate && window.ytUploadDate.enabled === false)
        ) {
            disablePlaylistVideoTitles();
            return;
        }

        let missingIds = details.playlist.filter(function(videoId) {
            return getCachedPlaylistVideoTitle(videoId) === "";
        });

        if(missingIds.length === 0) {
            applyPlaylistVideoTitles({});
            setPlaylistVideosStatus("", false);
            return;
        }

        let requestId = ++window.ytPlaylistVideos.requestId;
        window.ytPlaylistVideos.loading = true;
        setPlaylistVideosStatus("Loading titles…", true);

        let chunks = [];
        for(let i = 0; i < missingIds.length; i += 50) {
            chunks.push(missingIds.slice(i, i + 50));
        }

        let chain = $.Deferred().resolve({}).promise();
        chunks.forEach(function(chunk) {
            chain = chain.then(function(allTitles) {
                if(
                    requestId !== window.ytPlaylistVideos.requestId ||
                    window.ytPlaylistVideos.enabled === false
                ) {
                    return allTitles;
                }

                return fetchPlaylistVideoTitleChunk(chunk).then(function(chunkTitles) {
                    Object.keys(chunkTitles).forEach(function(videoId) {
                        allTitles[videoId] = chunkTitles[videoId];
                    });

                    if(
                        requestId === window.ytPlaylistVideos.requestId &&
                        window.ytPlaylistVideos.enabled !== false
                    ) {
                        applyPlaylistVideoTitles(chunkTitles);
                    }

                    return allTitles;
                });
            });
        });

        chain.always(function() {
            if(requestId !== window.ytPlaylistVideos.requestId) {
                return;
            }

            window.ytPlaylistVideos.loading = false;
            if(window.ytPlaylistVideos.enabled === false) {
                disablePlaylistVideoTitles();
            } else {
                setPlaylistVideosStatus("", false);
            }
        });
    }

    function playPlaylistVideoAt(index) {
        if(typeof index !== "number" || index < 0 || !window.player1) {
            return;
        }

        try {
            window.player1.playVideoAt(index);
        } catch(e) {
            console.log("Unable to open playlist video at index", index, e);
        }

        highlightPlaylistVideosCurrent(index);
        queuePlaylistPositionIndicatorRefresh();
    }

    function openPlaylistVideosPanel() {
        let details = getPlaylistPositionDetails();
        let $panel = $("#playlist-videos-panel");
        if(!details || !$panel.length) {
            return;
        }

        renderPlaylistVideosList(details);
        $panel.removeAttr("hidden");
        $("#playlist-position-label-btn").attr("aria-expanded", "true");
        loadPlaylistVideoTitles(details);
    }

    function togglePlaylistVideosPanel(event) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if(isPlaylistVideosPanelOpen()) {
            closePlaylistVideosPanel();
        } else {
            openPlaylistVideosPanel();
        }
    }

    function refreshPlaylistPositionIndicator() {
        let details = getPlaylistPositionDetails();
        if(!details) {
            setPlaylistPositionIndicator("", false);
            return;
        }

        let label = (details.playlistIndex + 1) + " / " + details.playlist.length;

        setPlaylistPositionIndicator(label, true);
        seedCurrentPlaylistVideoTitle(details);

        if(isPlaylistVideosPanelOpen()) {
            highlightPlaylistVideosCurrent(details.playlistIndex);
            let $items = $("#playlist-videos-list .playlist-videos-item");
            if($items.length !== details.playlist.length) {
                renderPlaylistVideosList(details);
                loadPlaylistVideoTitles(details);
            }
        }
    }

    function queuePlaylistPositionIndicatorRefresh() {
        [0, 250, 1000].forEach((delay)=>{
            setTimeout(refreshPlaylistPositionIndicator, delay);
        });
    }

    $(document).on("click.playlistVideosPanel", function(event) {
        if($(event.target).closest("#playlist-position-indicator").length === 0) {
            closePlaylistVideosPanel();
        }
    });

    // Optional via YOUTUBE_API_KEY in .env (shared with playlist video titles).
    // Missing/invalid key: upload date stays hidden; playback is unaffected.
    const UPLOAD_DATE_STORAGE_KEY = "YT__upload-date-cache";
    const UPLOAD_DATE_CACHE_MAX_ENTRIES = 2000;

    window.ytUploadDate = {
        enabled: null,
        memory: {},
        storage: null,
        requestId: 0,
        lastVideoId: "",
        lastCacheKey: "",
        inFlightVideoId: ""
    };

    function clearVideoUploadDate() {
        let $el = $("#video-upload-date");
        let $title = $("#video-title");
        let $slide = $("#app-meta-slide-video");
        if(!$el.length) {
            return;
        }
        $el.addClass("d-none").text("").removeAttr("datetime");
        $title.addClass("d-none").text("");
        $slide.addClass("d-none");
        refreshAppMetaCarousel();
    }

    function formatVideoUploadDate(publishedAt) {
        let date = new Date(publishedAt);
        if(Number.isNaN(date.getTime())) {
            return "";
        }

        try {
            return date.toLocaleDateString(undefined, {
                year: "numeric",
                month: "short",
                day: "numeric"
            });
        } catch(e) {
            return date.toISOString().slice(0, 10);
        }
    }

    function setVideoUploadDate(publishedAt, title) {
        let $el = $("#video-upload-date");
        let $title = $("#video-title");
        let $slide = $("#app-meta-slide-video");
        if(!$el.length) {
            return;
        }

        let label = formatVideoUploadDate(publishedAt);
        if(!label) {
            clearVideoUploadDate();
            return;
        }

        $el
            .attr("datetime", publishedAt)
            .text("Uploaded " + label)
            .removeClass("d-none");
        let videoTitle = typeof title === "string" ? title.trim() : "";
        $title
            .text(videoTitle)
            .toggleClass("d-none", !videoTitle);
        $slide.removeClass("d-none");
        refreshAppMetaCarousel("app-meta-slide-video");
    }

    window.appMetaCarousel = null;

    function initAppMetaCarousel() {
        let root = document.getElementById("app-meta-carousel");
        if(!root || window.appMetaCarousel) {
            return;
        }

        let allSlides = Array.from(root.querySelectorAll("[data-app-meta-slide]"));
        let dots = Array.from(root.querySelectorAll("[data-app-meta-carousel-dot]"));
        let previous = root.querySelector("[data-app-meta-carousel-previous]");
        let next = root.querySelector("[data-app-meta-carousel-next]");
        let controls = root.querySelector(".app-meta-carousel-controls");
        let activeId = "";

        function getAvailableSlides() {
            return allSlides.filter(function(slide) {
                return !slide.classList.contains("d-none");
            });
        }

        function refresh(preferredId) {
            let availableSlides = getAvailableSlides();
            let availableIds = availableSlides.map(function(slide) {
                return slide.id;
            });

            if(preferredId && availableIds.indexOf(preferredId) !== -1) {
                activeId = preferredId;
            } else if(availableIds.indexOf(activeId) === -1) {
                activeId = availableIds[0] || "";
            }

            allSlides.forEach(function(slide) {
                slide.hidden = slide.id !== activeId;
            });

            dots.forEach(function(dot) {
                let isAvailable = availableIds.indexOf(dot.getAttribute("aria-controls")) !== -1;
                let isActive = dot.getAttribute("aria-controls") === activeId;
                dot.classList.toggle("d-none", !isAvailable);
                dot.setAttribute("aria-pressed", isActive ? "true" : "false");
                dot.tabIndex = isActive ? 0 : -1;
            });

            if(controls) {
                controls.classList.toggle("d-none", availableSlides.length < 2);
            }
        }

        function move(step) {
            let availableSlides = getAvailableSlides();
            if(availableSlides.length < 2) {
                return;
            }

            let currentIndex = availableSlides.findIndex(function(slide) {
                return slide.id === activeId;
            });
            let nextIndex = (currentIndex + step + availableSlides.length) % availableSlides.length;
            activeId = availableSlides[nextIndex].id;
            refresh();
        }

        if(previous) {
            previous.addEventListener("click", function() {
                move(-1);
            });
        }
        if(next) {
            next.addEventListener("click", function() {
                move(1);
            });
        }
        dots.forEach(function(dot) {
            dot.addEventListener("click", function() {
                let targetId = dot.getAttribute("aria-controls");
                if(targetId) {
                    activeId = targetId;
                    refresh();
                }
            });
        });

        window.appMetaCarousel = {
            refresh: refresh
        };
        refresh();
    }

    function refreshAppMetaCarousel(preferredId) {
        if(window.appMetaCarousel && typeof window.appMetaCarousel.refresh === "function") {
            window.appMetaCarousel.refresh(preferredId);
        }
    }

    function normalizeUploadDateIdentityPart(value) {
        return String(value || "")
            .normalize("NFKC")
            .replace(/\s+/g, " ")
            .trim()
            .toLowerCase();
    }

    function buildUploadDateCacheKey(channelTitle, title) {
        let channel = normalizeUploadDateIdentityPart(channelTitle);
        let videoTitle = normalizeUploadDateIdentityPart(title);
        if(!channel || !videoTitle) {
            return "";
        }
        return channel + "\u0001" + videoTitle;
    }

    function getPlayerVideoIdentity(videoId) {
        let identity = {
            videoId: videoId || "",
            title: "",
            channelTitle: "",
            cacheKey: ""
        };

        try {
            if(window.player1 && typeof window.player1.getVideoData === "function") {
                let data = window.player1.getVideoData() || {};
                if(!identity.videoId && data.video_id) {
                    identity.videoId = data.video_id;
                }
                if(typeof data.title === "string") {
                    identity.title = data.title;
                }
                if(typeof data.author === "string") {
                    identity.channelTitle = data.author;
                }
            }
        } catch(e) {
        }

        identity.cacheKey = buildUploadDateCacheKey(identity.channelTitle, identity.title);
        return identity;
    }

    function loadUploadDateStorage() {
        if(window.ytUploadDate.storage) {
            return window.ytUploadDate.storage;
        }

        let storage = { v: 1, entries: {} };

        try {
            let raw = localStorage.getItem(UPLOAD_DATE_STORAGE_KEY);
            if(raw) {
                let parsed = JSON.parse(raw);
                if(parsed && typeof parsed === "object" && parsed.entries && typeof parsed.entries === "object") {
                    storage.entries = parsed.entries;
                }
            }
        } catch(e) {
            storage = { v: 1, entries: {} };
        }

        window.ytUploadDate.storage = storage;
        return storage;
    }

    function persistUploadDateStorage() {
        let storage = loadUploadDateStorage();
        let keys = Object.keys(storage.entries);

        if(keys.length > UPLOAD_DATE_CACHE_MAX_ENTRIES) {
            keys
                .map(function(key) {
                    let entry = storage.entries[key] || {};
                    return {
                        key: key,
                        savedAt: typeof entry.savedAt === "number" ? entry.savedAt : 0
                    };
                })
                .sort(function(a, b) {
                    return a.savedAt - b.savedAt;
                })
                .slice(0, keys.length - UPLOAD_DATE_CACHE_MAX_ENTRIES)
                .forEach(function(item) {
                    delete storage.entries[item.key];
                });
        }

        try {
            localStorage.setItem(UPLOAD_DATE_STORAGE_KEY, JSON.stringify(storage));
        } catch(e) {
            // Quota exceeded or private mode: keep the in-memory cache only.
        }
    }

    function readCachedUploadDate(cacheKey) {
        if(!cacheKey) {
            return undefined;
        }

        if(Object.prototype.hasOwnProperty.call(window.ytUploadDate.memory, cacheKey)) {
            return window.ytUploadDate.memory[cacheKey];
        }

        let storage = loadUploadDateStorage();
        if(!Object.prototype.hasOwnProperty.call(storage.entries, cacheKey)) {
            return undefined;
        }

        let entry = storage.entries[cacheKey];
        let publishedAt = entry && typeof entry.publishedAt === "string" ? entry.publishedAt : null;
        window.ytUploadDate.memory[cacheKey] = publishedAt;
        return publishedAt;
    }

    function writeCachedUploadDate(cacheKey, publishedAt, videoId) {
        if(!cacheKey) {
            return;
        }

        let value = publishedAt || null;
        window.ytUploadDate.memory[cacheKey] = value;

        let storage = loadUploadDateStorage();
        storage.entries[cacheKey] = {
            publishedAt: value,
            videoId: videoId || "",
            savedAt: Date.now()
        };
        persistUploadDateStorage();
    }

    function applyCachedUploadDate(publishedAt, title) {
        if(publishedAt) {
            setVideoUploadDate(publishedAt, title);
        } else {
            clearVideoUploadDate();
        }
    }

    function refreshVideoUploadDate(videoId) {
        if(window.ytUploadDate.enabled === false) {
            clearVideoUploadDate();
            return;
        }

        let identity = getPlayerVideoIdentity(videoId);
        if(!identity.videoId || typeof identity.videoId !== "string") {
            clearVideoUploadDate();
            return;
        }

        let sameVideoShowing =
            window.ytUploadDate.lastVideoId === identity.videoId &&
            window.ytUploadDate.lastCacheKey === identity.cacheKey &&
            !$("#video-upload-date").hasClass("d-none") &&
            !$("#video-title").hasClass("d-none");

        if(sameVideoShowing) {
            return;
        }

        window.ytUploadDate.lastVideoId = identity.videoId;
        window.ytUploadDate.lastCacheKey = identity.cacheKey;

        if(identity.cacheKey) {
            let cached = readCachedUploadDate(identity.cacheKey);
            if(typeof cached !== "undefined") {
                applyCachedUploadDate(cached, identity.title);
                return;
            }
        }

        if(window.ytUploadDate.inFlightVideoId === identity.videoId) {
            return;
        }

        let requestId = ++window.ytUploadDate.requestId;
        window.ytUploadDate.inFlightVideoId = identity.videoId;
        clearVideoUploadDate();

        $.ajax({
            url: "video-meta.php",
            data: { id: identity.videoId },
            dataType: "json",
            timeout: 9000
        }).done(function(data) {
            if(window.ytUploadDate.inFlightVideoId === identity.videoId) {
                window.ytUploadDate.inFlightVideoId = "";
            }

            if(!data || data.enabled === false) {
                if(requestId === window.ytUploadDate.requestId) {
                    window.ytUploadDate.enabled = false;
                    clearVideoUploadDate();
                }
                return;
            }

            window.ytUploadDate.enabled = true;
            let publishedAt = data.publishedAt || null;
            let cacheKey = buildUploadDateCacheKey(
                data.channelTitle || identity.channelTitle,
                data.title || identity.title
            );

            if(cacheKey) {
                writeCachedUploadDate(cacheKey, publishedAt, identity.videoId);
            }

            if(
                requestId !== window.ytUploadDate.requestId ||
                window.ytUploadDate.lastVideoId !== identity.videoId
            ) {
                return;
            }

            if(cacheKey) {
                window.ytUploadDate.lastCacheKey = cacheKey;
            }

            applyCachedUploadDate(publishedAt, data.title || identity.title);
        }).fail(function() {
            if(window.ytUploadDate.inFlightVideoId === identity.videoId) {
                window.ytUploadDate.inFlightVideoId = "";
            }

            if(requestId !== window.ytUploadDate.requestId) {
                return;
            }
            // Keep trying for later videos if the key exists but a single request failed.
            clearVideoUploadDate();
        });
    }

    function queueVideoUploadDateRefresh(videoId) {
        if(window.ytUploadDate.enabled === false) {
            return;
        }

        let id = videoId;
        if(!id) {
            try {
                id = getVideoId();
            } catch(e) {
                id = "";
            }
        }

        [0, 250, 1000].forEach((delay)=>{
            setTimeout(function() {
                let nextId = id;
                if(!nextId) {
                    try {
                        nextId = getVideoId();
                    } catch(e) {
                        nextId = "";
                    }
                }
                refreshVideoUploadDate(nextId);
            }, delay);
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

        if(
            window.ytDiagnostics.recoveryInFlight ||
            window.ytDiagnostics.iframeReloadInFlight ||
            window.ytDiagnostics.playerRebuildInFlight ||
            window.ytDiagnostics.pendingRestore !== null
        ) {
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
        lastWatchdogLogAt: 0,
        recoveryInFlight: false,
        lastRecoveryAt: 0,
        recoveryAttempts: 0,
        lastRecoveryVideoId: "",
        lastRecoveryTime: null,
        iframeReloadInFlight: false,
        playerRebuildInFlight: false,
        pendingRestore: null,
        referrerObserverStarted: false
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
        let playerHost = document.getElementById("player");
        let playerIframe = null;

        try { snapshot.state = window.player1.getPlayerState(); } catch(e) { snapshot.stateError = String(e); }
        try { snapshot.stateName = getPlayerStateName(snapshot.state); } catch(e) { snapshot.stateNameError = String(e); }
        try { snapshot.videoId = getVideoId(); } catch(e) { snapshot.videoIdError = String(e); }
        try { snapshot.currentTime = window.player1.getCurrentTime(); } catch(e) { snapshot.currentTimeError = String(e); }
        try { snapshot.duration = window.player1.getDuration(); } catch(e) { snapshot.durationError = String(e); }
        try { snapshot.playlistIndex = window.player1.getPlaylistIndex(); } catch(e) { snapshot.playlistIndexError = String(e); }
        try { snapshot.playlist = window.player1.getPlaylist(); } catch(e) { snapshot.playlistError = String(e); }
        try {
            if(typeof window.player1.getIframe === "function") {
                playerIframe = window.player1.getIframe();
            }
        } catch(e) {
            snapshot.iframeError = String(e);
        }
        if(!playerIframe && playerHost) {
            playerIframe = playerHost.querySelector("iframe");
        }
        snapshot.hasIframe = !!playerIframe;
        snapshot.hasInlineError = !!(playerHost && playerHost.querySelector(".ytp-error"));
        if(Array.isArray(snapshot.playlist)) {
            snapshot.playlistLength = snapshot.playlist.length;
            delete snapshot.playlist;
        }

        return snapshot;
    }

    function getPlayerIframeElement() {
        let playerHost = document.getElementById("player");
        let playerIframe = null;

        try {
            if(window.player1 && typeof window.player1.getIframe === "function") {
                playerIframe = window.player1.getIframe();
            }
        } catch(e) {
        }

        if(!playerIframe && playerHost) {
            playerIframe = playerHost.querySelector("iframe");
        }

        return playerIframe || null;
    }

    function configurePlayerIframe() {
        let playerIframe = getPlayerIframeElement();
        if(!playerIframe) {
            return;
        }

        // YouTube requires embedder identity via Referer; this is their recommended policy.
        playerIframe.setAttribute("referrerpolicy", "strict-origin-when-cross-origin");
        playerIframe.referrerPolicy = "strict-origin-when-cross-origin";
    }

    function watchPlayerIframeReferrer() {
        let playerHost = document.getElementById("player");
        if(!playerHost || typeof MutationObserver === "undefined") {
            return;
        }

        if(window.ytDiagnostics && window.ytDiagnostics.referrerObserverStarted) {
            configurePlayerIframe();
            return;
        }

        if(window.ytDiagnostics) {
            window.ytDiagnostics.referrerObserverStarted = true;
        }

        let observer = new MutationObserver(function() {
            configurePlayerIframe();
        });
        observer.observe(playerHost, { childList: true, subtree: true });
        configurePlayerIframe();
    }

    function clearRecoveryMemory() {
        window.ytDiagnostics.lastRecoveryTime = null;
        window.ytDiagnostics.pendingRestore = null;
        window.ytDiagnostics.recoveryInFlight = false;
        window.ytDiagnostics.iframeReloadInFlight = false;
        window.ytDiagnostics.playerRebuildInFlight = false;
    }

    function hasFatalPlayerSnapshot(snapshot, now) {
        if(!snapshot || !snapshot.hasPlayer) {
            return false;
        }

        let hasApiFailure = !!(
            snapshot.stateError ||
            snapshot.currentTimeError ||
            snapshot.durationError ||
            snapshot.videoIdError
        );

        if(hasApiFailure) {
            return true;
        }

        return !!snapshot.hasInlineError;
    }

    function trackRecoveryVideo(videoId) {
        if(!videoId) {
            return;
        }

        if(window.ytDiagnostics.lastRecoveryVideoId !== videoId) {
            window.ytDiagnostics.lastRecoveryVideoId = videoId;
            window.ytDiagnostics.lastCurrentTime = null;
            window.ytDiagnostics.lastProgressAt = 0;
            window.ytDiagnostics.recoveryAttempts = 0;
            clearRecoveryMemory();
        }
    }

    function resumePlaybackOnPlayer(player, target) {
        if(!player || !target) {
            return false;
        }

        let videoId = target.videoId || "";
        let playlistIndex = typeof target.playlistIndex === "number" ? target.playlistIndex : -1;
        let resumeFrom = typeof target.resumeFrom === "number" && isFinite(target.resumeFrom) ? Math.max(0, target.resumeFrom) : 0;

        if(loopVideoId !== null && videoId && typeof player.loadVideoById === "function") {
            player.loadVideoById(videoId, resumeFrom);
            return true;
        }

        if(playlistIndex >= 0 && typeof player.playVideoAt === "function") {
            player.playVideoAt(playlistIndex);
            setTimeout(()=>{
                try {
                    if(resumeFrom > 0 && typeof player.seekTo === "function") {
                        player.seekTo(resumeFrom, true);
                    }
                    if(typeof player.playVideo === "function") {
                        player.playVideo();
                    }
                } catch(e) {
                    console.log("Unable to finish playlist recovery", e);
                }
            }, 700);
            return true;
        }

        if(videoId && typeof player.loadVideoById === "function") {
            player.loadVideoById(videoId, resumeFrom);
            return true;
        }

        if(typeof player.playVideo === "function") {
            player.playVideo();
            return true;
        }

        return false;
    }

    function consumePendingRestore(player) {
        if(!window.ytDiagnostics.pendingRestore) {
            return false;
        }

        let target = window.ytDiagnostics.pendingRestore;
        window.ytDiagnostics.pendingRestore = null;
        window.ytDiagnostics.iframeReloadInFlight = false;
        window.ytDiagnostics.playerRebuildInFlight = false;

        console.warn("[YT] restoring playback after player rebuild", target);
        let didResume = resumePlaybackOnPlayer(player, target);
        if(!didResume) {
            window.ytDiagnostics.recoveryInFlight = false;
        }
        return didResume;
    }

    function rebuildPlayer(target) {
        if(window.ytDiagnostics.playerRebuildInFlight) {
            return;
        }

        window.ytDiagnostics.iframeReloadInFlight = false;
        window.ytDiagnostics.playerRebuildInFlight = true;
        window.ytDiagnostics.pendingRestore = target;

        console.warn("[YT] rebuilding player after stalled recovery", target);

        try {
            if(window.player1 && typeof window.player1.destroy === "function") {
                window.player1.destroy();
            }
        } catch(e) {
            console.log("Unable to destroy crashed player before rebuild", e);
        }

        let playerHost = document.getElementById("player");
        if(playerHost) {
            let freshPlayerHost = document.createElement("div");
            freshPlayerHost.id = "player";
            playerHost.replaceWith(freshPlayerHost);
        }

        window.player1 = null;

        setTimeout(()=>{
            try {
                onYouTubeIframeAPIReady();
            } catch(e) {
                window.ytDiagnostics.playerRebuildInFlight = false;
                window.ytDiagnostics.pendingRestore = null;
                window.ytDiagnostics.recoveryInFlight = false;
                console.error("[YT] player rebuild failed", e);
            }
        }, 100);

        setTimeout(()=>{
            if(window.ytDiagnostics.playerRebuildInFlight) {
                window.ytDiagnostics.playerRebuildInFlight = false;
                window.ytDiagnostics.pendingRestore = null;
                window.ytDiagnostics.recoveryInFlight = false;
                console.warn("[YT] player rebuild timed out");
            }
        }, 12000);
    }

    function reloadPlayerIframe(target) {
        if(window.ytDiagnostics.iframeReloadInFlight || window.ytDiagnostics.playerRebuildInFlight) {
            return false;
        }

        let playerIframe = getPlayerIframeElement();
        let iframeSrc = playerIframe && typeof playerIframe.src === "string" ? playerIframe.src : "";
        if(!playerIframe || !iframeSrc.length) {
            return false;
        }

        window.ytDiagnostics.iframeReloadInFlight = true;
        window.ytDiagnostics.pendingRestore = target;

        console.warn("[YT] reloading player iframe", {
            src: iframeSrc,
            videoId: target.videoId,
            playlistIndex: target.playlistIndex,
            resumeFrom: target.resumeFrom
        });

        configurePlayerIframe();
        playerIframe.src = "";

        setTimeout(()=>{
            try {
                playerIframe.src = iframeSrc;
            } catch(e) {
                console.log("Unable to restore player iframe src", e);
            }
        }, 150);

        setTimeout(()=>{
            window.ytDiagnostics.iframeReloadInFlight = false;
            try {
                if(window.ytDiagnostics.pendingRestore === target) {
                    if(resumePlaybackOnPlayer(window.player1, target)) {
                        window.ytDiagnostics.pendingRestore = null;
                    }
                }
            } catch(e) {
                console.log("Unable to resume after iframe reload", e);
            }
            window.ytDiagnostics.recoveryInFlight = false;
        }, 2500);

        setTimeout(()=>{
            if(window.ytDiagnostics.pendingRestore === target && !window.ytDiagnostics.playerRebuildInFlight) {
                rebuildPlayer(target);
            }
        }, 7000);

        return true;
    }

    function attemptPlaybackRecovery(snapshot, noProgressForMs) {
        if(!window.player1) {
            return;
        }

        let now = Date.now();
        let videoId = snapshot && snapshot.videoId ? snapshot.videoId : window.ytDiagnostics.lastVideoId;
        let playlistIndex = snapshot && typeof snapshot.playlistIndex === "number" ? snapshot.playlistIndex : -1;
        let resumeFrom = 0;

        trackRecoveryVideo(videoId);

        if(window.ytDiagnostics.recoveryInFlight) {
            return;
        }

        if(now - window.ytDiagnostics.lastRecoveryAt < 15000) {
            return;
        }

        if(window.ytDiagnostics.recoveryAttempts >= 3) {
            return;
        }

        if(snapshot && typeof snapshot.currentTime === "number" && isFinite(snapshot.currentTime)) {
            resumeFrom = snapshot.currentTime;
        }

        if(typeof window.ytDiagnostics.lastCurrentTime === "number" && isFinite(window.ytDiagnostics.lastCurrentTime)) {
            resumeFrom = Math.max(resumeFrom, window.ytDiagnostics.lastCurrentTime);
        }

        resumeFrom = Math.max(0, resumeFrom - 1.5);

        window.ytDiagnostics.recoveryInFlight = true;
        window.ytDiagnostics.lastRecoveryAt = now;
        window.ytDiagnostics.recoveryAttempts += 1;
        window.ytDiagnostics.lastRecoveryTime = resumeFrom;

        console.warn("[YT] attempting playback recovery", {
            videoId,
            playlistIndex,
            resumeFrom,
            noProgressForMs,
            recoveryAttempts: window.ytDiagnostics.recoveryAttempts
        });

        let recoveryTarget = {
            videoId,
            playlistIndex,
            resumeFrom,
            noProgressForMs,
            recoveryAttempts: window.ytDiagnostics.recoveryAttempts
        };

        if(window.ytDiagnostics.recoveryAttempts >= 3) {
            rebuildPlayer(recoveryTarget);
            return;
        }

        if(window.ytDiagnostics.recoveryAttempts >= 2) {
            if(!reloadPlayerIframe(recoveryTarget)) {
                rebuildPlayer(recoveryTarget);
            }
            return;
        }

        try {
            if(!resumePlaybackOnPlayer(window.player1, recoveryTarget)) {
                window.ytDiagnostics.recoveryInFlight = false;
            }
        } catch(e) {
            window.ytDiagnostics.recoveryInFlight = false;
            console.error("[YT] playback recovery failed", e);
            return;
        }

        setTimeout(()=>{
            if(Date.now() - window.ytDiagnostics.lastRecoveryAt >= 5000) {
                window.ytDiagnostics.recoveryInFlight = false;
            }
        }, 5000);
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
            trackRecoveryVideo(snapshot.videoId);
        }

        if(stateCode === YT.PlayerState.PLAYING) {
            window.ytDiagnostics.lastProgressAt = Date.now();
            if(typeof snapshot.currentTime === "number") {
                window.ytDiagnostics.lastCurrentTime = snapshot.currentTime;
                if(window.ytDiagnostics.lastRecoveryTime !== null && snapshot.currentTime >= Math.max(0, window.ytDiagnostics.lastRecoveryTime - 0.5)) {
                    clearRecoveryMemory();
                    window.ytDiagnostics.recoveryAttempts = 0;
                }
            }
        }

        console.log("[YT] state", {
            source,
            stateCode,
            stateName,
            snapshot
        });

        queuePlaylistPositionIndicatorRefresh();
        if(
            snapshot.videoId &&
            (
                stateCode === YT.PlayerState.CUED ||
                stateCode === YT.PlayerState.PLAYING ||
                stateCode === YT.PlayerState.BUFFERING
            )
        ) {
            queueVideoUploadDateRefresh(snapshot.videoId);
        }
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
            let hasFatalPlayerFailure = hasFatalPlayerSnapshot(snapshot, now);
            let previousCurrentTime = window.ytDiagnostics.lastCurrentTime;

            if(typeof snapshot.currentTime === "number") {
                if(previousCurrentTime === null || snapshot.currentTime > previousCurrentTime + 0.25) {
                    window.ytDiagnostics.lastCurrentTime = snapshot.currentTime;
                    window.ytDiagnostics.lastProgressAt = now;
                    if(window.ytDiagnostics.recoveryAttempts > 0) {
                        clearRecoveryMemory();
                        window.ytDiagnostics.recoveryAttempts = 0;
                    }
                }
            }

            let state = snapshot.state;
            let noProgressForMs = window.ytDiagnostics.lastProgressAt ? (now - window.ytDiagnostics.lastProgressAt) : 0;
            let shouldLogStall = false;
            let shouldAttemptRecovery = false;

            if(hasFatalPlayerFailure && !window.ytDiagnostics.playerRebuildInFlight) {
                console.error("[YT] player iframe became unusable", snapshot);
                attemptPlaybackRecovery(snapshot, noProgressForMs || 0);
                return;
            }

            if(state === YT.PlayerState.PLAYING && noProgressForMs > 12000) {
                shouldLogStall = true;
                shouldAttemptRecovery = true;
            }

            if(state === YT.PlayerState.BUFFERING && noProgressForMs > 15000) {
                shouldLogStall = true;
                shouldAttemptRecovery = true;
            }

            if(state === YT.PlayerState.UNSTARTED && noProgressForMs > 10000) {
                shouldLogStall = true;
                shouldAttemptRecovery = true;
            }

            if(state === YT.PlayerState.PAUSED && noProgressForMs > 10000) {
                shouldLogStall = true;
            }

            if(window.ytDiagnostics.iframeReloadInFlight) {
                return;
            }

            if(shouldLogStall && now - window.ytDiagnostics.lastWatchdogLogAt > 10000) {
                window.ytDiagnostics.lastWatchdogLogAt = now;
                console.error("[YT] playback stalled or stopped unexpectedly", {
                    lastState: window.ytDiagnostics.lastStateName,
                    noProgressForMs,
                    snapshot
                });
            }

            if(shouldAttemptRecovery) {
                attemptPlaybackRecovery(snapshot, noProgressForMs);
            }
        }, 4000);
    }
    </script>

    <style>
    .loop-btn:not(.active) i::before {
        content: "\f1b8";
    }
    .loop-btn.active i::before {
        content: "\f2f6";
    }
    .loop-btn.active {
        color: #bc232e;
        border-color: rgba(188, 35, 46, 0.35);
        background: rgba(188, 35, 46, 0.1);
    }
    </style>

</head>
  <body>
    <div id="playlist-name-flash" class="d-none"></div>

    <div id="player"></div>
    <div id="player-options" class="player-options">
        <div class="player-options-controls">
            <div class="player-options-row player-options-row-primary">
                <div id="playback-mode-control" class="playback-mode-control playback-mode-control--chronological">
                    <div class="playback-mode-head">
                        <label class="playback-mode-label" for="playback-mode-select">
                            <i class="fa fa-sort-amount-desc playback-mode-icon playback-mode-icon--chronological" aria-hidden="true"></i>
                            <i class="fa fa-random playback-mode-icon playback-mode-icon--shuffle" aria-hidden="true"></i>
                            <span class="playback-mode-label-text">Mode</span>
                        </label>
                        <button type="button" id="playback-mode-info-btn" class="playback-mode-info-btn" aria-label="About Mode" aria-expanded="false" aria-controls="playback-mode-info" onclick="togglePlaybackModeInfo(event);">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </button>
                    </div>
                    <select id="playback-mode-select" class="playback-mode-select" title="How videos start in the current playlist" aria-label="Playback mode" onchange="handlePlaybackModeChange();">
                        <option value="latest">Latest first</option>
                        <option value="shuffle">Shuffle</option>
                    </select>
                    <span id="playback-mode-override-note" class="playback-mode-override-note" hidden><i class="fa fa-tag" aria-hidden="true"></i> Set by playlist name</span>
                    <div id="playback-mode-info" class="playback-mode-info" role="dialog" aria-label="About Mode" tabindex="-1" hidden>
                        <p><strong>Latest first</strong> plays the playlist from its newest video down.</p>
                        <p><strong>Shuffle</strong> starts at a random video and keeps shuffling.</p>
                        <p>A playlist whose name has <code>(Shuffled)</code> or <code>(Shuffle)</code> always shuffles, and one with <code>(Chronological)</code> always plays latest first. Upper or lower case both work.</p>
                        <p>These only apply while that playlist is open. The next playlist without a tag goes back to the Mode you picked.</p>
                    </div>
                </div>
                <div id="playlist-position-indicator" class="player-options-nav d-none" role="group" aria-label="Playlist navigation">
                    <button type="button" class="playlist-position-arrow playlist-position-arrow-first" onclick="openFirstVideoInPlaylist();" aria-label="First video">
                        <span>1</span>
                    </button>
                    <button type="button" class="playlist-position-arrow playlist-position-arrow-prev" onclick="openPreviousVideoInPlaylist();" aria-label="Previous video">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <button type="button" id="playlist-position-label-btn" class="playlist-position-label" onclick="togglePlaylistVideosPanel(event);" aria-expanded="false" aria-controls="playlist-videos-panel" title="Show all playlist videos (titles need a YouTube API key)">
                        <span class="playlist-position-label-text"></span>
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="playlist-position-arrow playlist-position-arrow-next" onclick="openNextVideoInPlaylist();" aria-label="Next video">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                    <div id="playlist-videos-panel" class="playlist-videos-panel" hidden>
                        <div class="playlist-videos-heading">Playlist videos</div>
                        <div id="playlist-videos-list" class="playlist-videos-list" role="listbox" aria-label="Videos in this playlist"></div>
                        <div id="playlist-videos-status" class="playlist-videos-status d-none" aria-live="polite"></div>
                    </div>
                </div>
                <div id="watch-time-display" class="watch-time-display" aria-label="Watch time">
                    <span class="watch-time-heading">
                        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                        <span class="watch-time-heading-text">Watch time</span>
                    </span>
                    <span class="watch-time-values">
                        <button type="button" class="watch-time-stat watch-time-stat--today" title="Click to reset today's watch time" aria-label="Today watch time. Click to reset." onclick="handleWatchTimeClick(event, 'today');">
                            Today <strong id="watch-time-today-value">0s</strong>
                        </button>
                        <span class="watch-time-divider" aria-hidden="true">·</span>
                        <button type="button" class="watch-time-stat watch-time-stat--session" title="Click to reset session watch time" aria-label="Session watch time. Click to reset." onclick="handleWatchTimeClick(event, 'session');">
                            Session <strong id="watch-time-session-value">0s</strong>
                        </button>
                        <button type="button" class="watch-time-reset-all" title="Reset today and session watch time" aria-label="Reset both today and session watch time" onclick="handleWatchTimeClick(event, 'both');">
                            Reset both
                        </button>
                    </span>
                </div>
            </div>
            <div class="player-options-row player-options-row-brightness">
                <div id="brightness-control" class="brightness-control" aria-label="Page brightness">
                    <label for="brightness-slider" class="brightness-label">
                        <i class="fa fa-adjust" aria-hidden="true"></i>
                        <span class="brightness-label-text">Brightness</span>
                        <span class="brightness-value">100%</span>
                    </label>
                    <div class="brightness-track-wrap">
                        <div class="brightness-min-zone" aria-hidden="true" title="Minimum brightness (20%)"></div>
                        <div class="brightness-min-tick" aria-hidden="true"></div>
                        <input type="range" id="brightness-slider" min="20" max="100" value="100" step="1" aria-valuemin="20" aria-valuemax="100" aria-valuenow="100">
                    </div>
                    <div class="brightness-presets" role="group" aria-label="Brightness presets">
                        <button type="button" class="brightness-preset" data-brightness="33" aria-label="Set brightness to 33 percent">33%</button>
                        <button type="button" class="brightness-preset" data-brightness="50" aria-label="Set brightness to 50 percent">50%</button>
                        <button type="button" class="brightness-preset" data-brightness="75" aria-label="Set brightness to 75 percent">75%</button>
                        <button type="button" class="brightness-preset" data-brightness="100" aria-label="Set brightness to 100 percent">100%</button>
                    </div>
                    <span class="player-video-actions-divider" aria-hidden="true"></span>
                    <div class="player-video-actions" role="group" aria-label="Video actions">
                        <button type="button" class="player-video-action loop-btn" onclick="handleLoopBtn($(this));" aria-pressed="false" aria-label="Loop video" title="Loop the current video" data-toggle="tooltip"><i class="fa" aria-hidden="true"></i><span class="player-video-action-label">Loop video</span></button>
                        <button type="button" id="fit-video" class="player-video-action" onclick="$('html, body').scrollTop(0); $('#player').toggleClass('maximized'); event.stopPropagation();" aria-label="Fit video" title="Fit the video to the window" data-toggle="tooltip"><i class="fa fa-expand" aria-hidden="true"></i><span class="player-video-action-label">Fit video</span></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="app-meta" class="player-options-meta">
            <div id="app-meta-carousel" class="app-meta-carousel" role="region" aria-label="Video and app information">
                <div class="app-meta-carousel-viewport" aria-live="polite">
                    <section id="app-meta-slide-video" class="app-meta-carousel-slide d-none" data-app-meta-slide aria-label="Video details">
                        <details id="video-details" class="app-disclosure video-details">
                            <summary>
                                <span class="app-disclosure-label"><i class="fa fa-calendar app-disclosure-label-icon" aria-hidden="true"></i> Video details</span>
                                <span class="app-disclosure-preview">Title and upload date</span>
                            </summary>
                            <div class="app-disclosure-body video-details-body">
                                <strong id="video-title" class="video-title d-none"></strong>
                                <time id="video-upload-date" class="video-upload-date d-none" aria-live="polite" title="Video upload date"></time>
                            </div>
                        </details>
                    </section>
                    <section id="app-meta-slide-about" class="app-meta-carousel-slide" data-app-meta-slide aria-label="About this app">
                        <details id="app-desc" class="app-disclosure">
                            <summary>
                                <span class="app-disclosure-label"><i class="fa fa-info-circle app-disclosure-label-icon" aria-hidden="true"></i> About this app</span>
                                <span class="app-disclosure-preview">Youtube Viewer and Shuffler</span>
                            </summary>
                            <div class="app-disclosure-body">
                                <strong>Youtube Viewer and Shuffler</strong><br/>
                                By Weng. I can track my favorite channels and playlists. Why? Youtube changed how new videos show on my feed. Clicking a video just out of curiosity affects subsequent home views from showing my subscriptions. So something has gotta be done! Play youtube videos from favorited channels and playlists in order or randomly. You can fit the video to the window so you can watch on one part of the screen. As an advance internet user, you can place a playlist ID in the URL param as <code>?playlistId=...</code> or start the playlist at a song position with the URL param like <code>&playlistStart=1</code>. Want the video player fitted to the window when the page loads? Use URL parameter <code>fit-video</code>. As a developer, you can add more favorite channels or playlists at favs.php and /favs.
                            </div>
                        </details>
                    </section>
                    <section id="app-meta-slide-roadmap" class="app-meta-carousel-slide" data-app-meta-slide aria-label="Roadmap and support">
                        <details id="funding-needed" class="app-disclosure app-disclosure-support">
                            <summary>
                                <span class="app-disclosure-label"><i class="fa fa-compass app-disclosure-label-icon" aria-hidden="true"></i> Roadmap / support</span>
                                <span class="app-disclosure-preview">Funding, playlist editor, OAuth2</span>
                            </summary>
                            <div class="app-disclosure-body">
                                Funding needed. If only I have some time, I'll add an interface to add playlists or channels. Right now it's done through a JSON file on my side. I'd also add OAuth2 for your own profiles. Please <a href="https://www.wengindustry.com/me/contact" target="_blank" rel="noopener noreferrer">let me know</a> if you express interest in scaling up the app. Your name will be added and special requests possible.
                            </div>
                        </details>
                    </section>
                </div>
                <div class="app-meta-carousel-controls" role="group" aria-label="Information carousel controls">
                    <button type="button" class="app-meta-carousel-button" data-app-meta-carousel-previous aria-label="Previous information panel"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                    <div class="app-meta-carousel-dots" aria-label="Choose information panel">
                        <button type="button" class="app-meta-carousel-dot" data-app-meta-carousel-dot aria-label="Show video details" aria-controls="app-meta-slide-video" aria-pressed="false"></button>
                        <button type="button" class="app-meta-carousel-dot" data-app-meta-carousel-dot aria-label="Show about this app" aria-controls="app-meta-slide-about" aria-pressed="true"></button>
                        <button type="button" class="app-meta-carousel-dot" data-app-meta-carousel-dot aria-label="Show roadmap and support" aria-controls="app-meta-slide-roadmap" aria-pressed="false"></button>
                    </div>
                    <button type="button" class="app-meta-carousel-button" data-app-meta-carousel-next aria-label="Next information panel"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div id="player-fallback" class="alert alert-warning d-none" style="margin: 10px auto 0 auto; max-width: 640px;">
        <span class="fallback-reason">Embedded playback hit a YouTube player restriction.</span>
        <button class="btn btn-warning btn-xs" onclick="openPlaylistOnYoutube();" style="margin-left:10px;">Open YouTube</button>
    </div>
    <div class="spacer-v"></div>

    <div class="container-custom p-20">
        <div id="buttons">
            <div class="panel panel-warning control-card control-card-playlist">
                <div class="panel-heading" style="background-color:rgba(230,125,123,.75); color:rgba(255,255,255,0.75);">
                    <span onclick="More.counter('Playlistheader')"><i class="fa fa-list"></i> <span>Playlist</span></span>
                </div>
                <div class="panel-body">
                    <p class="control-hint control-hint-playlist">Step through your saved playlists, open a random playlist, open on Youtube, or load a playlist ID, playlist URL, or channel.</p>
                    <button class="btn btn-default control-btn control-btn-next" onclick='openNextPlaylist()'><i id="random" class="fa fa-caret-right clickable"></i><span>&nbsp;Next</span></button>
                    <button class="btn btn-default control-btn control-btn-prev" onclick='openPreviousPlaylist()'><i id="random" class="fa fa-xs fa-undo clickable"></i><span>&nbsp;Prev</span></button>
                    <button class="btn btn-default control-btn control-btn-random" onclick='$("#favs-wrapper iframe").contents().find("#random").click();' title="Open a random playlist from your favorites"><i id="random" class="fa fa-random clickable"></i><span>&nbsp;Random playlist</span></button>
                    <button class="btn btn-default control-btn control-btn-open-youtube" onclick='openPlaylistOnYoutube();'><i class="fa fa-external-link-alt clickable"></i><span>&nbsp;Open YouTube</span></button>
                    <button class="btn btn-secondary control-btn control-btn-playlist-id" onclick='$("#override-playlist-ui").toggleClass("fade d-none"); setTimeout(()=>{ $("#overridePlaylistId").focus(); }, 200);'><i id="manual" class="fa fa-cloud-upload-alt clickable"></i><span>&nbsp;Playlist ID</span></button>
                    <button class="btn btn-secondary control-btn control-btn-scroll" onclick='jumpToActivePlaylist();'><i id="manual" class="fa fa-ruler-vertical clickable"></i><span>&nbsp;Scroll</span></button>
                    <div id="override-playlist-ui" class="override-playlist-ui fade d-none">
                        <label for="overridePlaylistId" class="override-playlist-ui-label">Playlist ID, playlist URL, or channel</label>
                        <div class="override-playlist-ui-row">
                            <input id="overridePlaylistId" type="text" class="override-playlist-ui-input" onkeydown="if(event.key==='Enter'){ event.preventDefault(); overridePlaylistId(); }">
                            <button type="button" class="btn btn-primary override-playlist-ui-run" onclick="overridePlaylistId()">Run</button>
                        </div>
                        <div id="override-playlist-error" class="override-playlist-ui-error" role="alert"></div>
                    </div>
                    <!-- Disabled Auth button: Reason for it was some music videos require sign in. But now disabled because "Age-restricted videos can’t be watched on most 3rd party websites" -->
                    <!-- <button class="btn btn-secondary" onclick='window.open("https://accounts.google.com/signin/v2/identifier?service=youtube&uilel=3&passive=true&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26hl%3Den%26next%3Dhttps%253A%252F%252Fwww.youtube.com%252F&hl=en&ec=65620&flowName=GlifWebSignIn&flowEntry=ServiceLogin");' style="margin-top:5px;"><i id="manual" class="fa fa-user clickable"></i><span>&nbsp;Auth</span></button> -->
                </div>
            </div>

        </div>

        <!-- </div> --> <!-- Extra div? -->
        <div id="favs-section">
            <nav id="section-nav-parent" class="section-nav-parent" aria-label="Section navigation">
                <div class="section-nav-parent-header">
                    <div class="section-nav-parent-title-wrap">
                        <a class="section-nav-parent-title" href="javascript:void(0);" onclick="$('#favs-wrapper iframe')[0].contentWindow.fetchPlaylist('Default'); More.counter('Favheader');">Youtube uploads and playlists:</a>
                        <button type="button" class="section-nav-collections-btn" onclick="openFavCollectionsPanel();" title="Show playlist collections"><i class="fa fa-layer-group" aria-hidden="true"></i> Collections</button>
                    </div>
                    <div class="watch-history-split">
                        <button type="button" class="watch-history-open" id="watch-history-open" aria-expanded="false" aria-controls="watch-history-panel">History</button>
                        <span class="watch-history-split-sep" aria-hidden="true"></span>
                        <button type="button" class="watch-history-menu-toggle" id="watch-history-menu-toggle" aria-expanded="false" aria-haspopup="menu" aria-controls="watch-history-menu" aria-label="History actions">
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <div id="watch-history-menu" class="watch-history-menu" role="menu" hidden>
                            <button type="button" class="watch-history-menu-item" id="watch-history-clear" role="menuitem">Clear history</button>
                        </div>
                    </div>
                </div>
                <div class="section-nav-parent-controls">
                    <button class="section-nav-parent-btn section-nav-parent-prev" type="button" aria-label="Go to previous section" style="display:none;">
                        <span class="section-nav-parent-arrow">&larr;</span>
                        <span class="section-nav-parent-label"></span>
                    </button>
                    <span class="section-nav-parent-current" aria-live="polite"></span>
                    <button class="section-nav-parent-btn section-nav-parent-next" type="button" aria-label="Go to next section" style="display:none;">
                        <span class="section-nav-parent-label"></span>
                        <span class="section-nav-parent-arrow">&rarr;</span>
                    </button>
                    <button class="section-nav-parent-toggle" type="button" aria-expanded="false" aria-controls="section-nav-parent-list">
                        All topics
                        <span class="section-nav-parent-toggle-filter-badge" hidden><i class="fas fa-filter" aria-hidden="true"></i><span class="sr-only">(filtered)</span></span>
                        <span class="section-nav-parent-toggle-icon">&#9662;</span>
                    </button>
                </div>
                <div id="section-nav-parent-list" class="section-nav-parent-list" hidden>
                    <div class="section-nav-parent-list-toolbar">
                        <div class="section-nav-parent-list-tools">
                            <button class="section-nav-parent-alpha-toggle" type="button" aria-expanded="false" aria-controls="section-nav-parent-alpha-index" aria-label="Show letter jump by category" title="Jump by first letter of category">
                                <i class="fas fa-sort-alpha-down section-nav-parent-alpha-toggle-icon" aria-hidden="true"></i>
                                <span class="section-nav-parent-alpha-toggle-label">Jump · Category</span>
                            </button>
                            <div id="section-nav-parent-alpha-index" class="section-nav-parent-alpha-index" hidden></div>
                            <button class="section-nav-parent-view-toggle" type="button" aria-label="Switch to alphabetical list view" title="Alphabetical list">
                                <i class="fa fa-list" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="section-nav-parent-filter-bar" role="group" aria-label="Filter views">
                            <span class="section-nav-parent-filter-bar-label"><i class="fas fa-filter" aria-hidden="true"></i> Views</span>
                            <div class="section-nav-parent-filter-chips"></div>
                            <button class="section-nav-parent-filter-new" type="button" title="Save a set of shelves or categories as a filter view">
                                <i class="fas fa-plus" aria-hidden="true"></i> New view
                            </button>
                        </div>
                        <div class="section-nav-parent-filter-status" role="status" aria-live="polite" hidden></div>
                    </div>
                    <div class="section-nav-parent-list-body">
                        <div class="section-nav-parent-list-grid section-nav-parent-list-grid--mosaic"></div>
                    </div>
                </div>
                <div id="fav-collections-panel" class="fav-collections-panel" hidden aria-hidden="true">
                    <div class="fav-collections-panel-header">
                        <p class="fav-collections-panel-title">More favs collections</p>
                        <button type="button" class="fav-collections-panel-close" aria-label="Close collections" onclick="closeFavCollectionsPanel();">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="fav-collections-panel-body contents"></div>
                </div>
            </nav>
            <div id="favs-wrapper">
            </div>
        </div>
        <div id="discover-more">
            Discover new channels at: <a href="http://randomyt.com" target="_blank" rel="noopener noreferrer">RandomYt</a>.
        </div>

    </div> <!-- p-20 custom container -->

    <div id="category-browser" class="category-browser d-none">
        <button id="category-browser-top" class="category-browser-toggle category-browser-top" type="button" onclick="scrollToPageTop();" aria-label="Scroll to top" title="Scroll to top">
            <i class="fa fa-arrow-up" aria-hidden="true"></i>
        </button>
        <!-- Same action as the "Scroll" button in the Playlist panel -->
        <button id="category-browser-scroll" class="category-browser-toggle category-browser-scroll" type="button" onclick="jumpToActivePlaylist();" aria-label="Scroll to playlist" title="Scroll to playlist">
            <i class="fa fa-arrow-down" aria-hidden="true"></i>
        </button>
        <button id="category-browser-toggle" class="category-browser-toggle" type="button" onclick="toggleCategoryBrowser(event);" aria-label="Open playlist categories" aria-expanded="false">
            <i class="fa fa-tags"></i>
        </button>
        <div id="category-browser-panel" class="category-browser-panel" hidden>
            <div class="category-browser-title">Categories</div>
            <div class="category-browser-search-wrap">
                <label class="sr-only" for="category-browser-search">Search categories</label>
                <input id="category-browser-search" class="category-browser-search" type="search" placeholder="Search categories" autocomplete="off" aria-controls="category-browser-list" oninput="filterCategoryBrowser(); syncCategoryBrowserActive(false)">
            </div>
            <div id="category-browser-list" class="category-browser-list"></div>
            <p id="category-browser-empty" class="category-browser-empty" role="status" hidden>No matching categories.</p>
        </div>
    </div>

    <script>
    function stripHtmlFromLabel(labelText) {
        return $("<div>").html(labelText).text();
    }

    function extractCategoriesFromPlaylistLabel(labelText) {
        let cleanText = stripHtmlFromLabel(labelText);
        let matches = String(cleanText || "").match(/\[([^\]]+)\]/g) || [];
        return matches
            .map((match)=>match.slice(1, -1).trim())
            .filter((category)=>category.length > 0);
    }

    function escapeHtml(labelText) {
        return $("<div></div>").text(labelText).html();
    }

    function closeCategoryBrowser() {
        $("#category-browser-panel").attr("hidden", true);
        $("#category-browser-toggle").attr("aria-expanded", "false");
    }

    function filterCategoryBrowser() {
        var query = String($("#category-browser-search").val() || "").trim().toLowerCase();
        var $items = $("#category-browser-list .category-browser-item");
        var visibleCount = 0;
        $items.each(function() {
            var matches = !query || String(this.getAttribute("data-category") || "").toLowerCase().indexOf(query) !== -1;
            this.hidden = !matches;
            if(matches) visibleCount++;
        });
        $("#category-browser-empty").prop("hidden", visibleCount > 0);
    }

    function updateCategoryBrowserVisibility() {
        let $browser = $("#category-browser");
        let player = document.getElementById("player");

        if(!$browser.length || !player) {
            return;
        }

        let rect = player.getBoundingClientRect();
        let visibleWidth = Math.max(0, Math.min(rect.right, window.innerWidth) - Math.max(rect.left, 0));
        let visibleHeight = Math.max(0, Math.min(rect.bottom, window.innerHeight) - Math.max(rect.top, 0));
        let totalArea = Math.max(rect.width * rect.height, 1);
        let visibleAreaRatio = (visibleWidth * visibleHeight) / totalArea;
        let shouldHideBrowser = visibleAreaRatio > 0.5;

        // Hides the tag and up buttons only; the down arrow stays so the playlist is one tap away
        $browser.toggleClass("category-browser-hidden", shouldHideBrowser);
        $("#category-browser-toggle").attr("aria-hidden", shouldHideBrowser ? "true" : null).attr("tabindex", shouldHideBrowser ? "-1" : null);

        if(shouldHideBrowser) {
            closeCategoryBrowser();
        }

        liftCategoryBrowserAbovePlayerControls($browser[0], rect);
    }

    // If the player's bottom edge is on screen and reaches the corner, raise the buttons above its control bar
    function liftCategoryBrowserAbovePlayerControls(browserEl, playerRect) {
        const CONTROL_BAR = 56;
        const GAP = 8;
        let currentLift = parseFloat(browserEl.style.getPropertyValue("--category-browser-lift")) || 0;
        let groupRect = browserEl.getBoundingClientRect();
        // Where the group sits with no lift
        let restBottom = groupRect.bottom + currentLift;
        let restTop = restBottom - groupRect.height;
        let controlsTop = playerRect.bottom - CONTROL_BAR;
        let lift = 0;

        let bottomOnScreen = playerRect.bottom > 0 && playerRect.bottom <= window.innerHeight;
        let overlapsX = groupRect.left < playerRect.right && groupRect.right > playerRect.left;
        let overlapsY = restTop < playerRect.bottom && restBottom > controlsTop;
        if(bottomOnScreen && overlapsX && overlapsY) {
            lift = Math.max(0, restBottom - (controlsTop - GAP));
            // Never push the buttons off the top of the screen
            lift = Math.min(lift, Math.max(0, restTop - GAP));
        }

        if(Math.abs(lift - currentLift) > 0.5) {
            browserEl.style.setProperty("--category-browser-lift", lift + "px");
        }
    }

    function toggleCategoryBrowser(event) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }

        let $panel = $("#category-browser-panel");
        let isOpen = !$panel.is("[hidden]");

        if(isOpen) {
            closeCategoryBrowser();
        } else {
            $panel.removeAttr("hidden");
            $("#category-browser-toggle").attr("aria-expanded", "true");
            $("#category-browser-search").trigger("focus");
            syncCategoryBrowserActive(true);
        }
    }

    // ---- Tag panel: follow the section currently in view ----
    // The section in view comes from the "All topics" nav (window.parentSectionNav), which
    // already tracks it from page scroll. Only the panel's own list is scrolled here,
    // never the page.
    var categoryBrowserActiveLabel = null;

    function currentSectionLabelInView() {
        var nav = window.parentSectionNav;
        if(!nav || !nav.sections || nav.sections.length === 0) {
            return null;
        }
        nav.syncCurrentSection();
        var current = nav.sections[nav.currentIndex];
        return current ? String(current.label || "") : null;
    }

    function findCategoryBrowserItem(label) {
        if(!label) return $();
        var $items = $("#category-browser-list .category-browser-item:visible");
        var $exact = $items.filter(function() {
            return this.getAttribute("data-category") === label;
        });
        if($exact.length) return $exact.first();
        var lower = label.toLowerCase();
        return $items.filter(function() {
            return String(this.getAttribute("data-category") || "").toLowerCase() === lower;
        }).first();
    }

    // Keeps $item visible inside the list by scrolling the list only.
    function scrollCategoryBrowserListTo($item, instant) {
        var list = document.getElementById("category-browser-list");
        var item = $item[0];
        if(!list || !item) return;
        var listRect = list.getBoundingClientRect();
        var itemRect = item.getBoundingClientRect();
        var itemTop = itemRect.top - listRect.top + list.scrollTop;
        var itemBottom = itemTop + itemRect.height;
        var viewTop = list.scrollTop;
        var viewBottom = viewTop + list.clientHeight;
        if(itemTop >= viewTop && itemBottom <= viewBottom) return;
        var target = itemTop - (list.clientHeight - itemRect.height) / 2;
        target = Math.max(0, Math.min(target, list.scrollHeight - list.clientHeight));
        if(typeof list.scrollTo === "function") {
            list.scrollTo({ top: target, behavior: instant ? "auto" : "smooth" });
        } else {
            list.scrollTop = target;
        }
    }

    function syncCategoryBrowserActive(forceScroll) {
        if($("#category-browser-panel").is("[hidden]")) return;
        var label = currentSectionLabelInView();
        var $items = $("#category-browser-list .category-browser-item");
        var $active = findCategoryBrowserItem(label);
        var changed = label !== categoryBrowserActiveLabel;
        categoryBrowserActiveLabel = label;

        $items.not($active).removeClass("category-browser-item--active").removeAttr("aria-current");
        if(!$active.length) return;
        $active.addClass("category-browser-item--active").attr("aria-current", "location");
        if(changed || forceScroll) {
            scrollCategoryBrowserListTo($active, !!forceScroll);
        }
    }

    var categoryBrowserSyncPending = false;
    function scheduleCategoryBrowserSync() {
        if(categoryBrowserSyncPending || $("#category-browser-panel").is("[hidden]")) return;
        categoryBrowserSyncPending = true;
        requestAnimationFrame(function() {
            categoryBrowserSyncPending = false;
            syncCategoryBrowserActive(false);
        });
    }

    function scrollToPlaylistCategory(category) {
        let iframe = $("#favs-wrapper iframe")[0];
        if(!iframe || !iframe.contentWindow || !iframe.contentWindow.document) {
            return;
        }

        let $iframeBody = $(iframe.contentWindow.document);
        let $match = $iframeBody.find(".playlists-target a[data-playlist-id]").filter(function() {
            return $(this).is(":visible") && extractCategoriesFromPlaylistLabel($(this).text()).indexOf(category) !== -1;
        }).first();

        if(!$match.length) {
            return;
        }

        closeCategoryBrowser();

        // Land just below the sticky nav (it used to land under it), correcting once the scroll settles
        scrollPageToFavsElement($match[0], true);

        $match.addClass("category-jump-hit");
        setTimeout(()=>{
            $match.removeClass("category-jump-hit");
        }, 1800);
    }

    window.renderCategoryBrowser = function(categories) {
        let $browser = $("#category-browser");
        let $list = $("#category-browser-list");

        if(!$browser.length || !$list.length) {
            return;
        }

        if(!Array.isArray(categories) || categories.length === 0) {
            closeCategoryBrowser();
            $browser.addClass("d-none");
            $list.html("");
            $("#category-browser-search").val("");
            $("#category-browser-empty").prop("hidden", true);
            return;
        }

        $browser.removeClass("d-none");
        $list.html(
            categories.map((category)=>{
                var icon = (typeof window.playlistIconHtml === "function") ? window.playlistIconHtml(category) : "";
                return '<button type="button" class="category-browser-item" data-category="' + escapeHtml(category) + '" onclick="scrollToPlaylistCategory(this.getAttribute(\'data-category\'))">' + icon + '[' + escapeHtml(category) + ']</button>';
            }).join("")
        );

        $("#category-browser-toggle").attr("title", categories.length + " categories");
        filterCategoryBrowser();
        categoryBrowserActiveLabel = null;
        syncCategoryBrowserActive(true);
    };

    $("#category-browser-search").on("search", function() {
        filterCategoryBrowser();
        syncCategoryBrowserActive(false);
    }).on("keydown", function(event) {
        if(event.key === "Escape" && this.value) {
            this.value = "";
            filterCategoryBrowser();
            syncCategoryBrowserActive(false);
            event.preventDefault();
        }
    });

    $(document).on("click", function(event) {
        if($(event.target).closest("#category-browser").length===0) {
            closeCategoryBrowser();
        }
    });

    $(window).on("scroll resize", function() {
        updateCategoryBrowserVisibility();
        scheduleCategoryBrowserSync();
    });

    $(function() {
        updateCategoryBrowserVisibility();
        // "Fit video" and other player size changes move the player without scrolling
        let player = document.getElementById("player");
        if(player && window.ResizeObserver) {
            new ResizeObserver(function() {
                requestAnimationFrame(updateCategoryBrowserVisibility);
            }).observe(player);
        }
    });

    function showPlaylistGuide(instructions) {
        var text = String(instructions || "").trim();
        if(!text) {
            text = "Copy a playlist URL from the new tab, then paste it into Playlist ID.";
        }
        $("#playlist-guide-body").text(text);
        $("#playlist-guide-modal").modal("show");
    }

    function openPlaylistIdFromGuide() {
        $("#playlist-guide-modal").modal("hide");
        $("#override-playlist-ui").removeClass("fade d-none");
        setTimeout(function() {
            $("#overridePlaylistId").trigger("focus");
        }, 200);
    }

    function classifyPlaylistInput(raw) {
        var text = String(raw || "").trim();
        var playlistIdRe = /^[A-Za-z0-9_-]{10,}$/;
        var channelIdRe = /^UC[A-Za-z0-9_-]{22}$/;
        var tokenRe = /^[A-Za-z0-9._-]{1,80}$/;
        var reject = "Paste a playlist ID, a playlist URL, or a channel URL.";

        if(!text) return { error: reject };

        if(channelIdRe.test(text)) return { playlistId: "UU" + text.slice(2) };

        if(text.charAt(0)==="@") {
            var typedHandle = text.slice(1).split("/")[0];
            if(!tokenRe.test(typedHandle)) return { error: reject };
            return { lookup: { channelHandle: typedHandle } };
        }

        if(playlistIdRe.test(text)) return { playlistId: text };

        var withScheme = /^[a-z][a-z0-9+.-]*:\/\//i.test(text) ? text : "https://" + text;
        var url;
        try {
            url = new URL(withScheme);
        } catch (err) {
            return { error: reject };
        }

        var host = url.hostname.toLowerCase().replace(/\.$/, "").replace(/^(www|m|music)\./, "");
        if(host==="youtube-nocookie.com") host = "youtube.com";
        if(host!=="youtube.com" && host!=="youtu.be") return { error: reject };

        var parts = url.pathname.split("/").filter(Boolean).map(function(part) {
            try { return decodeURIComponent(part); } catch (err) { return part; }
        });
        var list = url.searchParams.get("list");

        if(parts[0]==="playlist") {
            if(list && playlistIdRe.test(list)) return { playlistId: list };
            return { error: "That playlist URL has no list id." };
        }

        if(parts[0]==="channel" && channelIdRe.test(parts[1] || "")) {
            return { playlistId: "UU" + parts[1].slice(2) };
        }

        if(parts[0] && parts[0].charAt(0)==="@") {
            var handle = parts[0].slice(1);
            if(!tokenRe.test(handle)) return { error: reject };
            return { lookup: { channelHandle: handle } };
        }

        if((parts[0]==="c" || parts[0]==="user") && parts[1]) {
            if(!tokenRe.test(parts[1])) return { error: reject };
            return { lookup: { channelKind: parts[0], channelSlug: parts[1] } };
        }

        if(list && playlistIdRe.test(list)) return { playlistId: list };

        return { error: reject };
    }

    function overridePlaylistId() {
        var parsed = classifyPlaylistInput($("#overridePlaylistId").val());
        var $error = $("#override-playlist-error");
        $error.text("");
        if(parsed.error) {
            $error.text(parsed.error);
            return;
        }
        if(parsed.playlistId) {
            window.parent.urlChange.playlist(null, parsed.playlistId);
            return;
        }
        $error.text("Looking up that channel…");
        $.getJSON("video-meta.php", parsed.lookup).done(function(data) {
            if(data && data.playlistId) {
                window.parent.urlChange.playlist(null, data.playlistId);
                return;
            }
            $error.text((data && data.error) || "Could not turn that channel into a playlist.");
        }).fail(function() {
            $error.text("Could not turn that channel into a playlist.");
        });
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
    watchPlayerIframeReferrer();
    window.player1 = new YT.Player("player", {
        height: '390',
        width: '640',
        videoId: loopVideoId, // Equivalent: player1.loadVideoById("3VqeJGcW680")
        playerVars: {
            origin: window.location.origin
        },
        events: {
            'onReady': function (event) {
                console.log("Using Youtube Iframe API with Youtube Video Looper algorithm ytVideoLooper");
                $("#player-fallback").addClass("d-none");
                configurePlayerIframe();
                startPlayerWatchdog();
                logPlayerStateChange({data: event.target.getPlayerState()}, "videoLooper:onReady");
                if(consumePendingRestore(event.target)) {
                    return;
                }
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
        watchPlayerIframeReferrer();
        // var shuffling = window.playlistStartIndex.length?true:false;
            window.player1 = new YT.Player("player", {
            height: '390',
            width: '640',
            playerVars: {
                playsinline: 1,
                origin: window.location.origin,
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
                    configurePlayerIframe();
                    startPlayerWatchdog();
                    logPlayerStateChange({data: event.target.getPlayerState()}, "playlist:onReady");
                    if(consumePendingRestore(event.target)) {
                        return;
                    }
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
        syncLoopBtnState($(".loop-btn"));
        eval(ytPlaylist);
    } else {
        $(".loop-btn").addClass("active");
        syncLoopBtnState($(".loop-btn"));
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

    // Parent-page sticky section nav
    class ParentSectionNav {
        constructor() {
            this.sections = [];
            this.currentIndex = 0;
            this.suppressSpy = false;
            this.rafPending = false;
            this.navHeight = 60;
            this.triggerBuffer = 24;
            this.pinnedTolerance = 80;
            this.listView = localStorage.getItem('YT__all-topics-view') === 'alphabetical' ? 'alphabetical' : 'mosaic';
            // Alphabetical table always opens sorted by Category A–Z
            this.listSortKey = 'category';
            this.listSortDir = 'asc';
            localStorage.removeItem('YT__all-topics-sort');
            // Letter jump row remembers whether it was left open
            this.alphaIndexExpanded = localStorage.getItem('YT__all-topics-alpha-open') === '1';
            // Saved filter views are loaded per collection and per All Topics layout.
            // Mosaic views stay shelf-only, while the alphabetical table keeps its
            // shelf-or-category views.
            this.filterViews = [];
            this.activeFilterId = null;
            this.filterScope = null;
            this.filterDraft = null;
            this.$filterModal = null;
        }

        init() {
            this.$nav = $('#section-nav-parent');
            this.$prevBtn = $('.section-nav-parent-prev');
            this.$nextBtn = $('.section-nav-parent-next');
            this.$current = $('.section-nav-parent-current');
            this.$toggle = $('.section-nav-parent-toggle');
            this.$list = $('#section-nav-parent-list');
            this.$listToolbar = $('.section-nav-parent-list-toolbar');
            this.$listBody = $('.section-nav-parent-list-body');
            this.$listGrid = $('.section-nav-parent-list-grid');
            this.$listTools = $('.section-nav-parent-list-tools');
            this.$viewToggle = $('.section-nav-parent-view-toggle');
            this.$alphaToggle = $('.section-nav-parent-alpha-toggle');
            this.$alphaIndex = $('.section-nav-parent-alpha-index');
            this.$filterBar = $('.section-nav-parent-filter-bar');
            this.$filterChips = $('.section-nav-parent-filter-chips');
            this.$filterNew = $('.section-nav-parent-filter-new');
            this.$filterStatus = $('.section-nav-parent-filter-status');

            this.loadFilterViews();
            this.syncSortForActiveView(true);
            this.bindEvents();
            this.updateViewToggleUi();
        }

        bindEvents() {
            this.$toggle.on('click', () => this.toggleList());
            this.$viewToggle.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.toggleListView();
            });
            this.$alphaToggle.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.toggleAlphaIndex();
            });
            this.$filterNew.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.openFilterModal(null, event.currentTarget);
            });
            this.$prevBtn.on('click', () => this.jumpToSection(this.currentIndex - 1));
            this.$nextBtn.on('click', () => this.jumpToSection(this.currentIndex + 1));

            $(window).on('scroll', () => this.onScroll());
            $(window).on('resize', () => this.onResize());
            this.updateStuckState();
        }

        updateStuckState() {
            if (!this.$nav.length) return;
            const sectionTop = $('#favs-section').offset().top;
            const isStuck = window.scrollY > sectionTop + 2;
            const wasStuck = this.$nav.hasClass('is-stuck');
            this.$nav.toggleClass('is-stuck', isStuck);
            if (isStuck !== wasStuck && this.$list && this.$list.length) {
                this.fitListBody();
            }
        }

        toggleList() {
            const expanded = this.$toggle.attr('aria-expanded') === 'true';
            this.$toggle.attr('aria-expanded', !expanded);
            if (expanded) {
                this.$list.attr('hidden', '');
            } else {
                this.$list.removeAttr('hidden');
                this.syncTableHeadOffset();
                this.fitListBody();
            }
            this.measureNavHeight();
        }

        collapseList() {
            this.$toggle.attr('aria-expanded', 'false');
            this.$list.attr('hidden', '');
        }

        getFirstLetter(label) {
            const trimmed = String(label || '').trim();
            if (!trimmed.length) {
                return '#';
            }

            const char = trimmed.charAt(0).toUpperCase();
            return /[A-Z]/.test(char) ? char : '#';
        }

        syncAlphaToggleUi() {
            if (!this.$alphaToggle || !this.$alphaToggle.length) {
                return;
            }

            const expanded = this.alphaIndexExpanded;
            const column = this.listSortKey === 'shelf' ? 'Shelf' : 'Category';
            const isDesc = this.listSortDir === 'desc';

            this.$alphaToggle
                .attr('aria-expanded', expanded ? 'true' : 'false')
                .attr('aria-label', `${expanded ? 'Hide' : 'Show'} letter jump by ${column.toLowerCase()}`)
                .attr('title', `Jump by first letter of ${column.toLowerCase()}`)
                .toggleClass('section-nav-parent-alpha-toggle--expanded', expanded);
            this.$alphaToggle.find('.section-nav-parent-alpha-toggle-icon')
                .attr('class', `fas ${isDesc ? 'fa-sort-alpha-down-alt' : 'fa-sort-alpha-down'} section-nav-parent-alpha-toggle-icon`);
            this.$alphaToggle.find('.section-nav-parent-alpha-toggle-label').text(`Jump · ${column}`);

            if (expanded) {
                this.renderAlphaIndex();
                this.$alphaIndex.removeAttr('hidden');
            } else {
                this.$alphaIndex.attr('hidden', '');
            }
        }

        toggleAlphaIndex() {
            this.alphaIndexExpanded = !this.alphaIndexExpanded;
            localStorage.setItem('YT__all-topics-alpha-open', this.alphaIndexExpanded ? '1' : '0');
            this.syncAlphaToggleUi();
            this.measureNavHeight();
        }

        renderAlphaIndex() {
            const letters = [];
            const seenLetters = {};

            this.$listGrid.find('[data-first-letter]').each((_, el) => {
                const letter = $(el).attr('data-first-letter');
                if (letter && !seenLetters[letter]) {
                    seenLetters[letter] = true;
                    letters.push(letter);
                }
            });

            // Letters follow the active sort column and direction
            letters.sort((a, b) => a.localeCompare(b));
            if (this.listSortDir === 'desc') {
                letters.reverse();
            }

            this.$alphaIndex.empty();
            letters.forEach((letter) => {
                const $btn = $('<button>')
                    .addClass('section-nav-parent-alpha-item')
                    .attr('type', 'button')
                    .attr('data-letter', letter)
                    .text(letter);
                $btn.on('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.scrollToLetter(letter);
                });
                this.$alphaIndex.append($btn);
            });
        }

        scrollToLetter(letter) {
            const $anchor = this.$listGrid.find(`[data-alpha-anchor="${letter}"]`).first();

            if (!$anchor.length || !this.$listBody.length) {
                return;
            }

            const bodyEl = this.$listBody[0];
            const anchorEl = $anchor.closest('tr')[0] || $anchor[0];
            const $tableHead = this.$listGrid.find('.section-nav-parent-table thead');
            const headerOffset = $tableHead.length ? $tableHead.outerHeight() : 0;
            const bodyRect = bodyEl.getBoundingClientRect();
            const anchorRect = anchorEl.getBoundingClientRect();
            const delta = anchorRect.top - bodyRect.top - 4 - headerOffset;
            const scrollTop = bodyEl.scrollTop + delta;

            this.$listBody.stop().animate({
                scrollTop: Math.max(scrollTop, 0)
            }, 200);
        }

        measureNavHeight() {
            this.navHeight = this.$nav.outerHeight() || 60;
        }

        buildSections() {
            this.sections = [];
            const $iframe = $('#favs-wrapper iframe');
            if (!$iframe.length) return;
            
            try {
                const $iframeContents = $iframe.contents();
                $iframeContents.find('[data-section-id]').each((i, el) => {
                    const $el = $(el);
                    const $parentLi = $el.closest('li');
                    if ($parentLi.is(':visible')) {
                        this.sections.push({
                            id: $el.attr('data-section-id'),
                            label: $el.attr('data-section-label') || stripHtmlFromLabel($el.text().replace(/^\[|\]$/g, '')),
                            group: $el.attr('data-section-group') || '',
                            el: el
                        });
                    }
                });
            } catch(e) {
                console.log('Cannot access iframe contents yet');
                return;
            }

            this.currentIndex = 0;
            // The iframe may have switched to another favs collection with its own views
            if (this.filterScope !== this.filterStorageScope()) {
                this.loadFilterViews();
                this.syncSortForActiveView(true);
            }
            this.renderAllTopicsList();
            this.measureNavHeight();
            if (this.sections.length > 0) {
                this.syncCurrentSection();
            }
            this.updateNav();
        }

        categoryLabelInGroup(label, group) {
            const prefix = group ? group + '/' : '';
            if (prefix && String(label).indexOf(prefix) === 0) {
                const rest = String(label).slice(prefix.length).trim();
                if (rest) return rest;
            }
            return label;
        }

        renderAllTopicsList() {
            this.$listGrid.empty();
            const isListView = this.listView === 'alphabetical';
            let items = this.sections.map((section, index) => ({ section, index }));
            const activeView = this.getActiveFilterView();
            const total = items.length;

            if (activeView) {
                items = items.filter(({ section }) => this.sectionMatchesView(section, activeView));
            }

            // Mosaic views arrange whole shelves. Keep each selected shelf together,
            // while the topics within a shelf retain their normal collection order.
            if (!isListView && activeView) {
                const shelfPosition = {};
                this.resolveMosaicShelfOrder(activeView).forEach((shelf, position) => {
                    shelfPosition[shelf] = position;
                });
                items.sort((a, b) => {
                    const aPosition = shelfPosition[a.section.group || ''];
                    const bPosition = shelfPosition[b.section.group || ''];
                    return (aPosition - bPosition) || a.index - b.index;
                });
            }

            const grouped = !isListView && items.some(({ section }) => section.group);
            this.$listGrid
                .removeClass('section-nav-parent-list-grid--list section-nav-parent-list-grid--mosaic section-nav-parent-list-grid--grouped')
                .addClass(isListView
                    ? 'section-nav-parent-list-grid--list'
                    : 'section-nav-parent-list-grid--mosaic')
                .toggleClass('section-nav-parent-list-grid--grouped', grouped);

            if (isListView) {
                this.syncSortForActiveView(false);
                this.$list.toggleClass('section-nav-parent-list--view-order', this.listSortKey === 'view');

                if (items.length || !activeView) {
                    this.renderAlphabeticalTable(items);
                } else {
                    this.renderFilterEmpty(activeView);
                }
                this.renderFilterBar(total, items.length);
                this.syncAlphaToggleUi();

                this.updateNav();
                return;
            }

            if (!items.length && activeView) {
                this.renderFilterEmpty(activeView);
                this.renderFilterBar(total, 0);
                this.updateNav();
                return;
            }

            let currentGroup = null;
            items.forEach(({ section, index }) => {
                if (grouped && section.group !== currentGroup) {
                    currentGroup = section.group;
                    const $group = $('<button>')
                        .addClass('section-nav-parent-group')
                        .attr('type', 'button')
                        .text(currentGroup || 'No shelf');
                    $group.on('click', () => {
                        this.jumpToSection(index);
                    });
                    this.$listGrid.append($group);
                }

                const $btn = $('<button>')
                    .addClass('section-nav-parent-item')
                    .attr('type', 'button')
                    .attr('data-section-index', index);
                this.fillLabeled($btn, section.label, grouped ? this.categoryLabelInGroup(section.label, section.group) : '');

                $btn.on('click', () => {
                    this.jumpToSection(index);
                });
                this.$listGrid.append($btn);
            });

            this.renderFilterBar(total, items.length);
            this.updateNav();
        }

        compareTopicLabels(a, b) {
            return String(a || '').localeCompare(String(b || ''), undefined, { sensitivity: 'base' });
        }

        sortAlphabeticalItems(items) {
            // The active view's saved order
            if (this.listSortKey === 'view') {
                const active = this.getActiveFilterView();
                const position = {};
                if (active) {
                    this.resolveViewOrder(active).forEach((section, i) => {
                        position[section.label] = i;
                    });
                }
                const pos = (item) => (position[item.section.label] !== undefined ? position[item.section.label] : 1e9);
                return items.slice().sort((a, b) => (pos(a) - pos(b)) || a.index - b.index);
            }

            const dir = this.listSortDir === 'desc' ? -1 : 1;
            const byShelf = this.listSortKey === 'shelf';

            return items.slice().sort((a, b) => {
                if (byShelf) {
                    const aShelf = a.section.group || '';
                    const bShelf = b.section.group || '';

                    // Categories without a shelf always sort last
                    if (!aShelf !== !bShelf) {
                        return aShelf ? -1 : 1;
                    }

                    const shelfCmp = this.compareTopicLabels(aShelf, bShelf) * dir;
                    if (shelfCmp) {
                        return shelfCmp;
                    }

                    return this.compareTopicLabels(a.section.label, b.section.label) || a.index - b.index;
                }

                return (this.compareTopicLabels(a.section.label, b.section.label) * dir) || a.index - b.index;
            });
        }

        renderAlphabeticalTable(items) {
            const sorted = this.sortAlphabeticalItems(items);
            const byShelf = this.listSortKey === 'shelf';
            const byView = this.listSortKey === 'view';
            const isDesc = this.listSortDir === 'desc';
            const seenAlphaAnchors = {};
            const activeView = this.getActiveFilterView();
            const hasViewOrder = this.viewHasCustomOrder(activeView);
            const $table = $('<table>')
                .addClass('section-nav-parent-table')
                .toggleClass('section-nav-parent-table--by-shelf', byShelf);
            const $headRow = $('<tr>');

            [
                { key: 'shelf', label: 'Shelf' },
                { key: 'category', label: 'Category' }
            ].forEach(({ key, label }) => {
                const isActive = this.listSortKey === key;
                const $th = $('<th>')
                    .addClass(`section-nav-parent-table-th section-nav-parent-table-th--${key}`)
                    .attr('scope', 'col')
                    .attr('aria-sort', isActive ? (isDesc ? 'descending' : 'ascending') : 'none');
                const $sortBtn = $('<button>')
                    .addClass('section-nav-parent-table-sort')
                    .toggleClass('section-nav-parent-table-sort--active', isActive)
                    .attr('type', 'button')
                    .attr('title', isActive
                        ? `Sorted by ${label.toLowerCase()} (${isDesc ? 'Z–A' : 'A–Z'}); click to reverse`
                        : `Sort by ${label.toLowerCase()}`)
                    .append($('<span>').text(label))
                    .append($('<i>')
                        .addClass(`fas ${isActive ? (isDesc ? 'fa-sort-down' : 'fa-sort-up') : 'fa-sort'} section-nav-parent-table-sort-icon`)
                        .attr('aria-hidden', 'true'));

                $sortBtn.on('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.setListSort(key);
                });

                $th.append($sortBtn);

                // Views with their own order get a "View order" pill: on when in use, a way back after a header sort
                if (key === 'category' && hasViewOrder) {
                    $th.addClass('section-nav-parent-table-th--has-view-order');
                    const $viewBtn = $('<button>')
                        .addClass('section-nav-parent-table-vieworder')
                        .toggleClass('section-nav-parent-table-vieworder--active', byView)
                        .attr('type', 'button')
                        .attr('aria-pressed', byView ? 'true' : 'false')
                        .attr('aria-label', byView ? 'View order (in use)' : 'Back to view order')
                        .attr('title', byView
                            ? `In "${activeView.name}" saved order. Click Shelf or Category to sort.`
                            : `Back to "${activeView.name}" saved order`)
                        .append(
                            $('<i>').addClass(`fas ${byView ? 'fa-list-ol' : 'fa-undo'}`).attr('aria-hidden', 'true'),
                            $('<span>').append($('<span>').addClass('section-nav-parent-table-vieworder-long').text('View '), 'order')
                        );
                    $viewBtn.on('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        this.setListSort('view');
                    });
                    $th.append($viewBtn);
                }

                $headRow.append($th);
            });

            const $tbody = $('<tbody>');
            const shelfCounts = {};
            let currentShelf = null;

            if (byShelf) {
                sorted.forEach(({ section }) => {
                    const shelf = section.group || '';
                    shelfCounts[shelf] = (shelfCounts[shelf] || 0) + 1;
                });
            }

            sorted.forEach(({ section, index }, position) => {
                const shelf = section.group || '';

                // Sorted by shelf: each shelf shows once as a sticky sub-header
                if (byShelf && shelf !== currentShelf) {
                    currentShelf = shelf;
                    const $groupCell = $('<th>')
                        .addClass('section-nav-parent-table-group-cell')
                        .attr('scope', 'colgroup')
                        .attr('colspan', 2)
                        .append($('<span>').addClass('section-nav-parent-table-group-name').text(shelf || 'No shelf'))
                        .append($('<span>').addClass('section-nav-parent-table-group-count').text(shelfCounts[shelf]));

                    if (shelf) {
                        const shelfLetter = this.getFirstLetter(shelf);
                        $groupCell.attr('data-first-letter', shelfLetter);
                        if (!seenAlphaAnchors[shelfLetter]) {
                            seenAlphaAnchors[shelfLetter] = true;
                            $groupCell.attr('data-alpha-anchor', shelfLetter);
                        }
                    }

                    $tbody.append($('<tr>').addClass('section-nav-parent-table-group').append($groupCell));
                }

                // The whole row is one button, so any click on the row jumps
                const $btn = $('<button>')
                    .addClass('section-nav-parent-item section-nav-parent-table-link')
                    .attr('type', 'button')
                    .attr('data-section-index', index)
                    .attr('aria-label', (!byShelf && shelf) ? `${section.label} (shelf: ${shelf})` : section.label);

                if (!byShelf) {
                    $btn.append($('<span>')
                        .addClass('section-nav-parent-table-shelf')
                        .toggleClass('section-nav-parent-table-shelf--empty', !shelf)
                        .text(shelf || '—'));
                }

                const $category = $('<span>').addClass('section-nav-parent-table-category');
                this.fillLabeled($category, section.label, byShelf ? this.categoryLabelInGroup(section.label, shelf) : '');
                if (byView) {
                    $category.prepend($('<span>').addClass('section-nav-parent-table-pos').attr('aria-hidden', 'true').text(position + 1));
                }
                $btn.append($category);

                if (!byShelf) {
                    const firstLetter = this.getFirstLetter(section.label);
                    $btn.attr('data-first-letter', firstLetter);
                    if (!seenAlphaAnchors[firstLetter]) {
                        seenAlphaAnchors[firstLetter] = true;
                        $btn.attr('data-alpha-anchor', firstLetter);
                    }
                }

                $btn.on('click', () => {
                    this.jumpToSection(index);
                });

                $tbody.append($('<tr>')
                    .addClass('section-nav-parent-table-row')
                    .append($('<td>')
                        .addClass('section-nav-parent-table-cell')
                        .attr('colspan', 2)
                        .append($btn)));
            });

            $table.append($('<thead>').append($headRow), $tbody);
            this.$listGrid.append($table);
            this.syncTableHeadOffset();
        }


        // ---- Filter views: saved shelf/category picks that narrow the alphabetical table ----
        // Saved per favs collection, since each collection has its own shelves and categories
        filterStorageScope() {
            const bundle = (typeof currentFavsBundle === 'function') ? currentFavsBundle() : '';
            return bundle || 'Default';
        }

        filterStorageKey(kind) {
            // Keep the original alphabetical keys for backwards compatibility.
            // Mosaic gets an independent set because it only supports shelves.
            const scopedKind = this.listView === 'mosaic' && (kind === 'views' || kind === 'active')
                ? `${kind}-mosaic`
                : kind;
            return `YT__all-topics-filter-${scopedKind}::${this.filterScope}`;
        }

        loadFilterViews() {
            this.filterScope = this.filterStorageScope();
            let parsed = [];
            try {
                parsed = JSON.parse(localStorage.getItem(this.filterStorageKey('views')) || '[]');
            } catch (e) {
                parsed = [];
            }

            const toStrings = (list) => Array.isArray(list) ? list.map((item) => String(item)) : [];
            this.filterViews = (Array.isArray(parsed) ? parsed : [])
                .filter((view) => view && typeof view === 'object' && view.id && String(view.name || '').trim())
                .map((view) => ({
                    id: String(view.id),
                    name: String(view.name).trim(),
                    shelves: toStrings(view.shelves),
                    categories: toStrings(view.categories),
                    // Saved topic order (category labels); empty means A–Z by category
                    order: toStrings(view.order)
                }));

            const activeId = localStorage.getItem(this.filterStorageKey('active'));
            this.activeFilterId = this.filterViews.some((view) => view.id === activeId) ? activeId : null;
        }

        saveFilterViews() {
            localStorage.setItem(this.filterStorageKey('views'), JSON.stringify(this.filterViews));
            if (this.activeFilterId) {
                localStorage.setItem(this.filterStorageKey('active'), this.activeFilterId);
            } else {
                localStorage.removeItem(this.filterStorageKey('active'));
            }
        }

        getActiveFilterView() {
            return this.filterViews.find((view) => view.id === this.activeFilterId) || null;
        }

        // A topic shows if its shelf OR its category is in the view; names no longer in the collection just match nothing
        sectionMatchesView(section, view) {
            const shelves = Array.isArray(view.shelves) ? view.shelves : [];
            const categories = Array.isArray(view.categories) ? view.categories : [];
            return shelves.indexOf(section.group || '') !== -1
                || categories.indexOf(section.label) !== -1;
        }

        countViewMatches(view) {
            return this.sections.filter((section) => this.sectionMatchesView(section, view)).length;
        }

        // Radio behavior: one view at a time; picking the active one again (or All) clears it
        setActiveFilter(id) {
            const exists = this.filterViews.some((view) => view.id === id);
            this.activeFilterId = (id && exists && id !== this.activeFilterId) ? id : null;
            this.syncSortForActiveView(true);
            this.saveFilterViews();
            this.renderAllTopicsList();
            this.$listBody.scrollTop(0);
            this.measureNavHeight();
            this.fitListBody();
        }

        // Topics a view matches, in the order they'll show: saved order first, newly matched topics appended A–Z
        resolveViewOrder(view) {
            const byLabel = {};
            const matched = [];
            this.sections.forEach((section) => {
                if (!byLabel[section.label] && this.sectionMatchesView(section, view)) {
                    byLabel[section.label] = section;
                    matched.push(section);
                }
            });

            const ordered = [];
            const used = {};
            (view.order || []).forEach((label) => {
                if (byLabel[label] && !used[label]) {
                    used[label] = true;
                    ordered.push(byLabel[label]);
                }
            });
            matched
                .filter((section) => !used[section.label])
                .sort((a, b) => this.compareTopicLabels(a.label, b.label))
                .forEach((section) => ordered.push(section));
            return ordered;
        }

        // A Mosaic view selects and reorders shelves, not individual topics.
        // Missing shelves are retained in a saved order only until they reappear.
        resolveMosaicShelfOrder(view) {
            const available = [];
            const seen = {};
            this.sections.forEach((section) => {
                const shelf = section.group || '';
                if (this.sectionMatchesView(section, view) && !seen[shelf]) {
                    seen[shelf] = true;
                    available.push(shelf);
                }
            });

            const ordered = [];
            const used = {};
            (view.order || []).forEach((shelf) => {
                if (seen[shelf] && !used[shelf]) {
                    used[shelf] = true;
                    ordered.push(shelf);
                }
            });
            available
                .filter((shelf) => !used[shelf])
                .sort((a, b) => {
                    if (!a !== !b) return a ? -1 : 1;
                    return this.compareTopicLabels(a, b);
                })
                .forEach((shelf) => ordered.push(shelf));
            return ordered;
        }

        viewHasCustomOrder(view) {
            return !!(view && Array.isArray(view.order) && view.order.length);
        }

        // A view with its own order opens in that order; without one, fall back to Category A–Z
        syncSortForActiveView(preferViewOrder) {
            const active = this.getActiveFilterView();
            if (this.viewHasCustomOrder(active)) {
                if (preferViewOrder) {
                    this.listSortKey = 'view';
                    this.listSortDir = 'asc';
                }
            } else if (this.listSortKey === 'view') {
                this.listSortKey = 'category';
                this.listSortDir = 'asc';
            }
        }

        filterDraftView() {
            return {
                shelves: Object.keys(this.filterDraft.shelves),
                categories: Object.keys(this.filterDraft.categories),
                order: this.filterDraft.order
            };
        }

        filterModalTabs() {
            return this.filterDraft && this.filterDraft.mosaic ? ['shelves', 'order'] : ['shelves', 'categories', 'order'];
        }

        // This modal is shared between layouts, but Category is only available
        // in Alphabetical. Render the control only where it has a real action.
        renderFilterModalTabs() {
            const $tabs = this.$filterModal.find('.yt-filter-modal-tabs');
            $tabs.find('[data-filter-tab="categories"]').remove();

            if (this.filterDraft.mosaic) {
                return;
            }

            const $categoryTab = $('<button>')
                .attr({
                    type: 'button',
                    role: 'tab',
                    id: 'yt-filter-tab-categories',
                    'data-filter-tab': 'categories',
                    'aria-controls': 'yt-filter-modal-panel'
                })
                .addClass('yt-filter-modal-tab')
                .append(
                    $('<span>').addClass('yt-filter-modal-tab-by').text('By '),
                    'category ',
                    $('<span>').addClass('yt-filter-modal-tab-count').attr('data-filter-count', 'categories')
                );
            $categoryTab.insertBefore($tabs.find('[data-filter-tab="order"]'));
        }

        filterOrderEntries() {
            const draft = this.filterDraftView();
            if (!this.filterDraft.mosaic) {
                return this.resolveViewOrder(draft).map((section) => ({
                    label: section.label,
                    name: section.label,
                    meta: section.group || '',
                    icon: true
                }));
            }

            return this.resolveMosaicShelfOrder(draft).map((shelf) => ({
                label: shelf,
                name: shelf || 'No shelf',
                meta: `${this.sections.filter((section) => (section.group || '') === shelf).length} categories`
            }));
        }

        // Order tab: the table orders topics; Mosaic orders whole shelves.
        renderFilterOrder(focusLabel, focusSelector) {
            const ordered = this.filterOrderEntries();
            const custom = this.filterDraft.order.length > 0;
            const $list = this.$filterOrderList.empty();
            const isMosaic = this.filterDraft.mosaic;

            this.$filterOrder.find('.yt-filter-order-reset').prop('disabled', !custom);
            this.$filterOrder.find('.yt-filter-order-state').text(custom ? 'Custom order' : (isMosaic ? 'A–Z by shelf' : 'A–Z by category'));

            if (!ordered.length) {
                $list.append($('<li>')
                    .addClass('yt-filter-order-empty')
                    .text(isMosaic ? 'Pick shelves first. They show up here for reordering.' : 'Pick shelves or categories first. The topics they match show up here.'));
                return;
            }

            ordered.forEach((entry, i) => {
                const label = entry.label;
                const $name = $('<span>').addClass('yt-filter-order-name');
                if (entry.icon) {
                    this.fillLabeled($name, entry.name);
                } else {
                    $name.text(entry.name);
                }

                $list.append($('<li>')
                    .addClass('yt-filter-order-item')
                    .attr('data-label', label)
                    .append(
                        $('<button>')
                            .addClass('yt-filter-order-handle')
                            .attr('type', 'button')
                            .attr('aria-label', `Reorder ${label}, position ${i + 1} of ${ordered.length}. Drag, or press Up or Down.`)
                            .attr('title', 'Drag to reorder (or Alt+↑/↓)')
                            .append($('<i>').addClass('fas fa-grip-vertical').attr('aria-hidden', 'true')),
                        $('<span>').addClass('yt-filter-order-pos').attr('aria-hidden', 'true').text(i + 1),
                        $('<span>').addClass('yt-filter-order-text').append(
                            $name,
                            entry.meta ? $('<span>').addClass('yt-filter-order-meta').text(entry.meta) : null
                        ),
                        $('<button>')
                            .addClass('yt-filter-order-move')
                            .attr('type', 'button')
                            .attr('data-move', '-1')
                            .attr('aria-label', `Move ${label} up`)
                            .prop('disabled', i === 0)
                            .append($('<i>').addClass('fas fa-chevron-up').attr('aria-hidden', 'true')),
                        $('<button>')
                            .addClass('yt-filter-order-move')
                            .attr('type', 'button')
                            .attr('data-move', '1')
                            .attr('aria-label', `Move ${label} down`)
                            .prop('disabled', i === ordered.length - 1)
                            .append($('<i>').addClass('fas fa-chevron-down').attr('aria-hidden', 'true'))
                    ));
            });

            if (focusLabel) {
                const $item = $list.children().filter((_, el) => el.getAttribute('data-label') === focusLabel);
                let $target = $item.find(focusSelector || '.yt-filter-order-handle');
                if (!$target.length || $target.prop('disabled')) {
                    $target = $item.find('.yt-filter-order-handle');
                }
                $target.trigger('focus');
                const el = $item[0];
                if (el && el.scrollIntoView) {
                    el.scrollIntoView({ block: 'nearest' });
                }
            }
        }

        announceFilterOrder(message) {
            this.$filterOrder.find('.yt-filter-order-live').text(message);
        }

        moveFilterOrderItem(label, delta, focusSelector) {
            const labels = this.filterOrderEntries().map((entry) => entry.label);
            const from = labels.indexOf(label);
            const to = from + delta;
            if (from < 0 || to < 0 || to >= labels.length) {
                return;
            }

            labels.splice(to, 0, labels.splice(from, 1)[0]);
            this.filterDraft.order = labels;
            this.renderFilterOrder(label, focusSelector);
            this.announceFilterOrder(`${label} moved to position ${to + 1} of ${labels.length}`);
            this.updateFilterModalSummary();
        }

        resetFilterOrder() {
            this.filterDraft.order = [];
            this.renderFilterOrder();
            this.announceFilterOrder(`Order reset to A–Z by ${this.filterDraft.mosaic ? 'shelf' : 'category'}`);
            this.updateFilterModalSummary();
            this.$filterOrder.find('.yt-filter-order-list .yt-filter-order-handle').first().trigger('focus');
        }

        // Pointer-event drag (mouse, pen, touch). The row follows the pointer; a line marks the drop spot.
        startFilterOrderDrag(event) {
            const e = event.originalEvent || event;
            if (e.button !== undefined && e.button !== 0) {
                return;
            }
            event.preventDefault();

            const handle = event.currentTarget;
            const item = handle.closest('.yt-filter-order-item');
            const listEl = this.$filterOrderList[0];
            if (!item || !listEl) {
                return;
            }

            const pointerId = e.pointerId;
            const startY = e.clientY;
            const startScroll = listEl.scrollTop;
            const others = Array.prototype.slice.call(listEl.querySelectorAll('.yt-filter-order-item')).filter((el) => el !== item);
            const fromIndex = Array.prototype.indexOf.call(listEl.children, item);
            let lastY = startY;
            let targetIndex = fromIndex;
            let frame = null;
            let moved = false;

            try {
                handle.setPointerCapture(pointerId);
            } catch (err) {}
            item.classList.add('yt-filter-order-item--dragging');
            listEl.classList.add('yt-filter-order-list--dragging');

            const clearMarks = () => {
                others.forEach((el) => el.classList.remove('yt-filter-order-item--drop-before', 'yt-filter-order-item--drop-after'));
            };

            const place = () => {
                item.style.transform = `translateY(${lastY - startY + (listEl.scrollTop - startScroll)}px)`;
                targetIndex = 0;
                others.forEach((el) => {
                    const rect = el.getBoundingClientRect();
                    if (lastY > rect.top + rect.height / 2) {
                        targetIndex++;
                    }
                });
                clearMarks();
                if (targetIndex !== fromIndex) {
                    if (targetIndex < others.length) {
                        others[targetIndex].classList.add('yt-filter-order-item--drop-before');
                    } else if (others.length) {
                        others[others.length - 1].classList.add('yt-filter-order-item--drop-after');
                    }
                }
            };

            // Scroll the list when dragging near its top or bottom edge
            const autoScroll = () => {
                const rect = listEl.getBoundingClientRect();
                const edge = 40;
                let step = 0;
                if (lastY < rect.top + edge) {
                    step = -10;
                } else if (lastY > rect.bottom - edge) {
                    step = 10;
                }
                if (!step) {
                    frame = null;
                    return;
                }
                listEl.scrollTop += step;
                place();
                frame = requestAnimationFrame(autoScroll);
            };

            const onMove = (ev) => {
                if (ev.pointerId !== pointerId) {
                    return;
                }
                ev.preventDefault();
                lastY = ev.clientY;
                moved = moved || Math.abs(lastY - startY) > 3;
                place();
                if (!frame) {
                    frame = requestAnimationFrame(autoScroll);
                }
            };

            const onEnd = (ev) => {
                if (ev.pointerId !== pointerId) {
                    return;
                }
                handle.removeEventListener('pointermove', onMove);
                handle.removeEventListener('pointerup', onEnd);
                handle.removeEventListener('pointercancel', onEnd);
                if (frame) {
                    cancelAnimationFrame(frame);
                }
                clearMarks();
                item.style.transform = '';
                item.classList.remove('yt-filter-order-item--dragging');
                listEl.classList.remove('yt-filter-order-list--dragging');

                const label = item.getAttribute('data-label');
                if (ev.type === 'pointerup' && moved && targetIndex !== fromIndex) {
                    const labels = others.map((el) => el.getAttribute('data-label'));
                    labels.splice(targetIndex, 0, label);
                    this.filterDraft.order = labels;
                    this.renderFilterOrder(label);
                    this.announceFilterOrder(`${label} moved to position ${targetIndex + 1} of ${labels.length}`);
                    this.updateFilterModalSummary();
                } else {
                    handle.focus();
                }
            };

            handle.addEventListener('pointermove', onMove);
            handle.addEventListener('pointerup', onEnd);
            handle.addEventListener('pointercancel', onEnd);
        }

        renderFilterBar(total, shown) {
            if (!this.$filterChips || !this.$filterChips.length) {
                return;
            }

            const active = this.getActiveFilterView();
            const applied = !!active;

            this.$filterChips.empty();

            const $all = $('<button>')
                .addClass('section-nav-parent-filter-chip section-nav-parent-filter-chip--all')
                .toggleClass('section-nav-parent-filter-chip--active', !active)
                .attr('type', 'button')
                .attr('aria-pressed', active ? 'false' : 'true')
                .attr('title', 'Show every topic')
                .append($('<span>').addClass('section-nav-parent-filter-chip-name').text('All'));
            $all.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.setActiveFilter(null);
            });
            this.$filterChips.append($all);

            this.filterViews.forEach((view) => {
                const isActive = view.id === this.activeFilterId;
                const matches = this.countViewMatches(view);
                const $chip = $('<div>')
                    .addClass('section-nav-parent-filter-chip section-nav-parent-filter-chip--view')
                    .toggleClass('section-nav-parent-filter-chip--active', isActive);
                const $apply = $('<button>')
                    .addClass('section-nav-parent-filter-chip-apply')
                    .attr('type', 'button')
                    .attr('aria-pressed', isActive ? 'true' : 'false')
                    .attr('title', isActive ? `Showing "${view.name}"; click to show all` : `Show only "${view.name}" (${matches} topics)`)
                    .append(isActive ? $('<i>').addClass('fas fa-check section-nav-parent-filter-chip-check').attr('aria-hidden', 'true') : null)
                    .append($('<span>').addClass('section-nav-parent-filter-chip-name').text(view.name))
                    .append($('<span>').addClass('section-nav-parent-filter-chip-count').text(matches));
                const $edit = $('<button>')
                    .addClass('section-nav-parent-filter-chip-edit')
                    .attr('type', 'button')
                    .attr('aria-label', `Edit view "${view.name}"`)
                    .attr('title', 'Edit or delete this view')
                    .append($('<i>').addClass('fas fa-pen').attr('aria-hidden', 'true'));

                $apply.on('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.setActiveFilter(view.id);
                });
                $edit.on('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.openFilterModal(view.id, event.currentTarget);
                });

                this.$filterChips.append($chip.append($apply, $edit));
            });

            // Clear signs that a filter is on: banner above the table, tinted list, badge on "All topics"
            this.$list.toggleClass('section-nav-parent-list--filtered', applied);
            this.$toggle
                .toggleClass('section-nav-parent-toggle--filtered', applied)
                .attr('title', applied ? `All topics (filtered by "${active.name}")` : 'All topics');
            this.$toggle.find('.section-nav-parent-toggle-filter-badge').prop('hidden', !applied);

            if (!applied) {
                this.$filterStatus.attr('hidden', '').empty();
                return;
            }

            const $clear = $('<button>')
                .addClass('section-nav-parent-filter-clear')
                .attr('type', 'button')
                .append($('<i>').addClass('fa fa-times').attr('aria-hidden', 'true'), ' Clear');
            $clear.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.setActiveFilter(null);
            });

            this.$filterStatus
                .empty()
                .append(
                    $('<i>').addClass('fas fa-filter section-nav-parent-filter-status-icon').attr('aria-hidden', 'true'),
                    $('<span>').addClass('section-nav-parent-filter-status-text').append(
                        'Filtered by ',
                        $('<strong>').text(`"${active.name}"`),
                        $('<span>').addClass('section-nav-parent-filter-status-count').text(` · showing ${shown} of ${total}`)
                    ),
                    $clear
                )
                .removeAttr('hidden');
        }

        renderFilterEmpty(view) {
            const $clear = $('<button>')
                .addClass('section-nav-parent-filter-empty-btn')
                .attr('type', 'button')
                .text('Show all topics');
            const $edit = $('<button>')
                .addClass('section-nav-parent-filter-empty-btn')
                .attr('type', 'button')
                .text('Edit view');
            $clear.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.setActiveFilter(null);
            });
            $edit.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.openFilterModal(view.id, event.currentTarget);
            });

            this.$listGrid.append($('<div>')
                .addClass('section-nav-parent-filter-empty')
                .append(
                    $('<p>').addClass('section-nav-parent-filter-empty-title').text(`No topics match "${view.name}"`),
                    $('<p>').addClass('section-nav-parent-filter-empty-text').text(this.listView === 'mosaic'
                        ? 'Its shelves aren\u2019t in this collection right now.'
                        : 'Its shelves and categories aren\u2019t in this collection right now.'),
                    $('<div>').addClass('section-nav-parent-filter-empty-actions').append($edit, $clear)
                ));
        }

        buildFilterModal() {
            if (this.$filterModal) {
                return;
            }

            const $modal = $(`
                <div class="yt-filter-modal" hidden>
                    <div class="yt-filter-modal-backdrop" data-filter-close></div>
                    <div class="yt-filter-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="yt-filter-modal-title" aria-describedby="yt-filter-modal-desc">
                        <div class="yt-filter-modal-header">
                            <h2 id="yt-filter-modal-title" class="yt-filter-modal-title">New filter view</h2>
                            <button type="button" class="yt-filter-modal-close" data-filter-close aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </div>
                        <form class="yt-filter-modal-form" novalidate>
                            <div class="yt-filter-modal-body">
                                <label class="yt-filter-modal-label" for="yt-filter-modal-name">Name</label>
                                <input id="yt-filter-modal-name" class="yt-filter-modal-name" type="text" maxlength="60" autocomplete="off" placeholder="e.g. Faith, Games, Weekend">
                                <p class="yt-filter-modal-error" role="alert" hidden></p>
                                <p id="yt-filter-modal-desc" class="yt-filter-modal-hint">Pick shelves, categories, or both. A topic shows if its shelf or its category is picked.</p>
                                <div class="yt-filter-modal-tabs" role="tablist" aria-label="Pick by">
                                    <button type="button" role="tab" id="yt-filter-tab-shelves" class="yt-filter-modal-tab" data-filter-tab="shelves" aria-controls="yt-filter-modal-panel"><span class="yt-filter-modal-tab-by">By </span>shelf <span class="yt-filter-modal-tab-count" data-filter-count="shelves"></span></button>
                                    <button type="button" role="tab" id="yt-filter-tab-order" class="yt-filter-modal-tab" data-filter-tab="order" aria-controls="yt-filter-modal-panel"><i class="fas fa-sort-amount-down" aria-hidden="true"></i> Order <span class="yt-filter-modal-tab-count yt-filter-modal-tab-count--text" data-filter-count="order"></span></button>
                                </div>
                                <div id="yt-filter-modal-panel" class="yt-filter-modal-panel" role="tabpanel">
                                    <div class="yt-filter-modal-panel-tools">
                                        <input type="search" class="yt-filter-modal-search" autocomplete="off" aria-label="Search">
                                        <button type="button" class="yt-filter-modal-link" data-filter-select="all">Select all</button>
                                        <button type="button" class="yt-filter-modal-link" data-filter-select="none">None</button>
                                    </div>
                                    <div class="yt-filter-modal-options" role="group"></div>
                                    <div class="yt-filter-order" hidden>
                                        <div class="yt-filter-order-tools">
                                            <p class="yt-filter-order-hint"><span class="yt-filter-order-state">A–Z by category</span> · drag <i class="fas fa-grip-vertical" aria-hidden="true"></i> or use the arrows</p>
                                            <button type="button" class="yt-filter-modal-link yt-filter-order-reset"><i class="fas fa-sort-alpha-down" aria-hidden="true"></i> Reset to A–Z</button>
                                        </div>
                                        <ol class="yt-filter-order-list" aria-label="Topic order"></ol>
                                        <p class="yt-filter-order-live sr-only" aria-live="polite"></p>
                                    </div>
                                </div>
                                <p class="yt-filter-modal-summary" aria-live="polite"></p>
                            </div>
                            <div class="yt-filter-modal-footer">
                                <div class="yt-filter-modal-delete-wrap">
                                    <button type="button" class="yt-filter-modal-btn yt-filter-modal-btn--danger-ghost yt-filter-modal-delete"><i class="fas fa-trash-alt" aria-hidden="true"></i> Delete</button>
                                    <span class="yt-filter-modal-confirm" hidden>
                                        <span class="yt-filter-modal-confirm-text">Delete this view?</span>
                                        <button type="button" class="yt-filter-modal-btn yt-filter-modal-btn--danger yt-filter-modal-confirm-yes">Delete</button>
                                        <button type="button" class="yt-filter-modal-btn yt-filter-modal-confirm-no">Keep</button>
                                    </span>
                                </div>
                                <div class="yt-filter-modal-actions">
                                    <button type="button" class="yt-filter-modal-btn" data-filter-close>Cancel</button>
                                    <button type="submit" class="yt-filter-modal-btn yt-filter-modal-btn--primary yt-filter-modal-save">Save view</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            $('body').append($modal);
            this.$filterModal = $modal;
            this.$filterDialog = $modal.find('.yt-filter-modal-dialog');
            this.$filterName = $modal.find('.yt-filter-modal-name');
            this.$filterError = $modal.find('.yt-filter-modal-error');
            this.$filterSearch = $modal.find('.yt-filter-modal-search');
            this.$filterOptions = $modal.find('.yt-filter-modal-options');
            this.$filterSummary = $modal.find('.yt-filter-modal-summary');
            this.$filterOrder = $modal.find('.yt-filter-order');
            this.$filterOrderList = $modal.find('.yt-filter-order-list');

            this.$filterOrderList.on('pointerdown', '.yt-filter-order-handle', (event) => this.startFilterOrderDrag(event));
            this.$filterOrderList.on('click', '.yt-filter-order-move', (event) => {
                event.preventDefault();
                const $btn = $(event.currentTarget);
                const delta = parseInt($btn.attr('data-move'), 10);
                this.moveFilterOrderItem(
                    $btn.closest('.yt-filter-order-item').attr('data-label'),
                    delta,
                    `.yt-filter-order-move[data-move="${delta}"]`
                );
            });
            this.$filterOrderList.on('keydown', '.yt-filter-order-item', (event) => {
                if (event.key !== 'ArrowUp' && event.key !== 'ArrowDown') {
                    return;
                }
                const onHandle = $(event.target).hasClass('yt-filter-order-handle');
                if (!event.altKey && !onHandle) {
                    return;
                }
                event.preventDefault();
                event.stopPropagation();
                const delta = event.key === 'ArrowUp' ? -1 : 1;
                const focusSelector = onHandle ? '.yt-filter-order-handle' : `.yt-filter-order-move[data-move="${delta}"]`;
                this.moveFilterOrderItem($(event.currentTarget).attr('data-label'), delta, focusSelector);
            });
            $modal.find('.yt-filter-order-reset').on('click', (event) => {
                event.preventDefault();
                this.resetFilterOrder();
            });

            $modal.on('click', '[data-filter-close]', (event) => {
                event.preventDefault();
                this.closeFilterModal();
            });
            $modal.on('click', '[data-filter-tab]', (event) => {
                event.preventDefault();
                this.setFilterModalTab($(event.currentTarget).attr('data-filter-tab'), true);
            });
            $modal.on('keydown', '[role="tab"]', (event) => {
                if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                    event.preventDefault();
                    const tabs = this.filterModalTabs();
                    const step = event.key === 'ArrowRight' ? 1 : tabs.length - 1;
                    this.setFilterModalTab(tabs[(tabs.indexOf(this.filterDraft.tab) + step) % tabs.length], true);
                    $modal.find(`[data-filter-tab="${this.filterDraft.tab}"]`).trigger('focus');
                }
            });
            $modal.on('click', '[data-filter-select]', (event) => {
                event.preventDefault();
                const selectAll = $(event.currentTarget).attr('data-filter-select') === 'all';
                const picked = this.filterDraft[this.filterDraft.tab];
                this.$filterOptions.find('.yt-filter-modal-option:not([hidden]) input').each((_, input) => {
                    if (selectAll) {
                        picked[input.value] = true;
                    } else {
                        delete picked[input.value];
                    }
                    input.checked = selectAll;
                });
                this.updateFilterModalSummary();
            });
            this.$filterOptions.on('change', 'input[type="checkbox"]', (event) => {
                const picked = this.filterDraft[this.filterDraft.tab];
                if (event.target.checked) {
                    picked[event.target.value] = true;
                } else {
                    delete picked[event.target.value];
                }
                $(event.target).closest('.yt-filter-modal-option').toggleClass('yt-filter-modal-option--checked', event.target.checked);
                this.updateFilterModalSummary();
            });
            this.$filterSearch.on('input', () => this.applyFilterModalSearch());
            this.$filterSearch.on('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
            this.$filterName.on('input', () => this.$filterError.attr('hidden', ''));
            $modal.find('.yt-filter-modal-form').on('submit', (event) => {
                event.preventDefault();
                this.saveFilterModal();
            });
            $modal.find('.yt-filter-modal-delete').on('click', (event) => {
                event.preventDefault();
                this.showFilterDeleteConfirm(true);
            });
            $modal.find('.yt-filter-modal-confirm-no').on('click', (event) => {
                event.preventDefault();
                this.showFilterDeleteConfirm(false);
            });
            $modal.find('.yt-filter-modal-confirm-yes').on('click', (event) => {
                event.preventDefault();
                this.deleteFilterView(this.filterDraft.id);
            });
            $modal.on('keydown', (event) => this.onFilterModalKeydown(event));
        }

        openFilterModal(viewId, opener) {
            this.buildFilterModal();
            const view = viewId ? this.filterViews.find((item) => item.id === viewId) : null;
            const toMap = (list) => (list || []).reduce((map, key) => { map[key] = true; return map; }, {});
            const isMosaic = this.listView === 'mosaic';

            this.filterModalOpener = opener || document.activeElement;
            this.filterDraft = {
                id: view ? view.id : null,
                shelves: toMap(view ? view.shelves : []),
                categories: isMosaic ? {} : toMap(view ? view.categories : []),
                order: view && Array.isArray(view.order) ? view.order.slice() : [],
                mosaic: isMosaic,
                tab: (!isMosaic && view && !view.shelves.length && view.categories.length) ? 'categories' : 'shelves'
            };

            this.$filterModal.find('.yt-filter-modal-title').text(view ? 'Edit filter view' : 'New filter view');
            this.$filterModal.find('.yt-filter-modal-save').text(view ? 'Save changes' : 'Save view');
            this.$filterModal.find('.yt-filter-modal-delete-wrap').toggle(!!view);
            this.renderFilterModalTabs();
            this.$filterModal.find('#yt-filter-modal-desc').text(isMosaic
                ? 'Pick shelves to show in this mosaic view, then arrange the shelves in the order you want.'
                : 'Pick shelves, categories, or both. A topic shows if its shelf or its category is picked.');
            this.$filterModal.find('.yt-filter-order-reset').html(isMosaic
                ? '<i class="fas fa-sort-alpha-down" aria-hidden="true"></i> Reset to A–Z'
                : '<i class="fas fa-sort-alpha-down" aria-hidden="true"></i> Reset to A–Z');
            this.$filterOrderList.attr('aria-label', isMosaic ? 'Shelf order' : 'Topic order');
            this.showFilterDeleteConfirm(false);
            this.$filterName.val(view ? view.name : '');
            this.$filterError.attr('hidden', '').text('');
            this.setFilterModalTab(this.filterDraft.tab, false);

            this.$filterModal.removeAttr('hidden');
            $('body').addClass('yt-filter-modal-open');
            setTimeout(() => this.$filterName.trigger('focus'), 0);
        }

        closeFilterModal() {
            if (!this.$filterModal || this.$filterModal.is('[hidden]')) {
                return;
            }

            this.$filterModal.attr('hidden', '');
            $('body').removeClass('yt-filter-modal-open');
            const opener = this.filterModalOpener;
            this.filterModalOpener = null;
            if (opener && document.body.contains(opener)) {
                opener.focus();
            } else if (this.$filterNew && this.$filterNew.length) {
                this.$filterNew.trigger('focus');
            }
        }

        onFilterModalKeydown(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                event.stopPropagation();
                if (!this.$filterModal.find('.yt-filter-modal-confirm').is('[hidden]')) {
                    this.showFilterDeleteConfirm(false);
                } else {
                    this.closeFilterModal();
                }
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            // Keep Tab inside the dialog
            const focusables = this.$filterDialog
                .find('button, input, [tabindex]:not([tabindex="-1"])')
                .filter((_, el) => !el.disabled && $(el).is(':visible'));
            if (!focusables.length) {
                return;
            }

            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }

        // Shelf and category choices, plus any saved picks that are no longer in this collection
        filterModalOptions(kind) {
            const picked = this.filterDraft[kind];
            const options = [];
            const seen = {};

            if (kind === 'shelves') {
                const counts = {};
                this.sections.forEach((section) => {
                    const shelf = section.group || '';
                    counts[shelf] = (counts[shelf] || 0) + 1;
                });
                Object.keys(counts).forEach((shelf) => {
                    seen[shelf] = true;
                    options.push({
                        value: shelf,
                        name: shelf || 'No shelf',
                        meta: `${counts[shelf]} ${counts[shelf] === 1 ? 'category' : 'categories'}`,
                        sortKey: shelf
                    });
                });
            } else {
                this.sections.forEach((section) => {
                    if (seen[section.label]) {
                        return;
                    }
                    seen[section.label] = true;
                    options.push({
                        value: section.label,
                        name: section.label,
                        meta: section.group || '',
                        sortKey: section.label,
                        icon: true
                    });
                });
            }

            Object.keys(picked).forEach((value) => {
                if (!seen[value]) {
                    options.push({
                        value,
                        name: value || 'No shelf',
                        meta: 'Not in this collection',
                        sortKey: value,
                        missing: true
                    });
                }
            });

            return options.sort((a, b) => {
                // Picks missing from this collection come first so they're easy to spot and uncheck
                if (!a.missing !== !b.missing) {
                    return a.missing ? -1 : 1;
                }
                // "No shelf" sorts last
                if (!a.sortKey !== !b.sortKey) {
                    return a.sortKey ? -1 : 1;
                }
                return this.compareTopicLabels(a.sortKey, b.sortKey);
            });
        }

        setFilterModalTab(tab, focusSearch) {
            const tabs = this.filterModalTabs();
            this.filterDraft.tab = tabs.indexOf(tab) !== -1 ? tab : 'shelves';
            const isShelves = this.filterDraft.tab === 'shelves';
            const isOrder = this.filterDraft.tab === 'order';

            this.$filterModal.find('[data-filter-tab]').each((_, el) => {
                const selected = $(el).attr('data-filter-tab') === this.filterDraft.tab;
                $(el)
                    .attr('aria-selected', selected ? 'true' : 'false')
                    .attr('tabindex', selected ? '0' : '-1')
                    .toggleClass('yt-filter-modal-tab--active', selected);
            });
            this.$filterModal.find('.yt-filter-modal-panel')
                .attr('aria-labelledby', `yt-filter-tab-${this.filterDraft.tab}`);
            this.$filterModal.find('.yt-filter-modal-panel-tools, .yt-filter-modal-options').prop('hidden', isOrder);
            this.$filterOrder.prop('hidden', !isOrder);

            if (isOrder) {
                this.renderFilterOrder();
                this.updateFilterModalSummary();
                return;
            }

            this.$filterSearch
                .val('')
                .attr('placeholder', isShelves ? 'Search shelves' : 'Search categories')
                .attr('aria-label', isShelves ? 'Search shelves' : 'Search categories');

            this.renderFilterModalOptions();
            if (focusSearch) {
                this.$filterSearch.trigger('focus');
            }
        }

        renderFilterModalOptions() {
            const kind = this.filterDraft.tab;
            const picked = this.filterDraft[kind];
            const options = this.filterModalOptions(kind);

            this.$filterOptions.empty().attr('aria-label', kind === 'shelves' ? 'Shelves' : 'Categories');

            if (!options.length) {
                this.$filterOptions.append($('<p>')
                    .addClass('yt-filter-modal-none')
                    .text(kind === 'shelves' ? 'This collection has no shelves.' : 'No categories loaded yet.'));
            }

            options.forEach((option) => {
                const checked = !!picked[option.value];
                const $name = $('<span>').addClass('yt-filter-modal-option-name');
                if (option.icon) {
                    this.fillLabeled($name, option.name);
                } else {
                    $name.text(option.name);
                }

                const $option = $('<label>')
                    .addClass('yt-filter-modal-option')
                    .toggleClass('yt-filter-modal-option--checked', checked)
                    .toggleClass('yt-filter-modal-option--missing', !!option.missing)
                    .attr('data-search', `${option.name} ${option.meta}`.toLowerCase())
                    .append(
                        $('<input>').attr('type', 'checkbox').val(option.value).prop('checked', checked),
                        $('<span>').addClass('yt-filter-modal-option-text').append(
                            $name,
                            option.meta ? $('<span>').addClass('yt-filter-modal-option-meta').text(option.meta) : null
                        )
                    );
                this.$filterOptions.append($option);
            });

            this.$filterOptions.append($('<p>').addClass('yt-filter-modal-none yt-filter-modal-no-match').attr('hidden', '').text('No matches'));
            this.$filterOptions.scrollTop(0);
            this.updateFilterModalSummary();
        }

        applyFilterModalSearch() {
            const query = String(this.$filterSearch.val() || '').trim().toLowerCase();
            let visible = 0;
            this.$filterOptions.find('.yt-filter-modal-option').each((_, el) => {
                const match = !query || String($(el).attr('data-search')).indexOf(query) !== -1;
                $(el).prop('hidden', !match);
                if (match) visible++;
            });
            this.$filterOptions.find('.yt-filter-modal-no-match').prop('hidden', visible > 0 || !query);
        }

        updateFilterModalSummary() {
            const shelves = Object.keys(this.filterDraft.shelves);
            const categories = this.filterDraft.mosaic ? [] : Object.keys(this.filterDraft.categories);
            const draftView = { shelves, categories };
            const matches = this.countViewMatches(draftView);

            this.$filterModal.find('[data-filter-count="shelves"]').text(shelves.length || '');
            this.$filterModal.find('[data-filter-count="categories"]').text(categories.length || '');
            this.$filterModal.find('[data-filter-count="order"]').text(this.filterDraft.order.length ? 'Custom' : '');

            if (!shelves.length && !categories.length) {
                this.$filterSummary.text('Nothing picked yet');
                return;
            }

            const plural = (count, one, many) => `${count} ${count === 1 ? one : many}`;
            if (this.filterDraft.mosaic) {
                this.$filterSummary.text(
                    `${plural(shelves.length, 'shelf', 'shelves')} picked → shows ${plural(matches, 'topic', 'topics')} of ${this.sections.length}`
                );
                return;
            }
            this.$filterSummary.text(
                `${plural(shelves.length, 'shelf', 'shelves')} · ${plural(categories.length, 'category', 'categories')} picked → shows ${plural(matches, 'topic', 'topics')} of ${this.sections.length}`
            );
        }

        showFilterModalError(message, $focus) {
            this.$filterError.text(message).removeAttr('hidden');
            if ($focus) {
                $focus.trigger('focus');
            }
        }

        saveFilterModal() {
            const name = String(this.$filterName.val() || '').trim().replace(/\s+/g, ' ');
            const shelves = Object.keys(this.filterDraft.shelves);
            const isMosaic = this.filterDraft.mosaic;
            const categories = isMosaic ? [] : Object.keys(this.filterDraft.categories);
            const editingId = this.filterDraft.id;
            // Keep a custom order only for entries the view still matches.
            const order = this.filterDraft.order.length
                ? (isMosaic
                    ? this.resolveMosaicShelfOrder({ shelves, categories, order: this.filterDraft.order })
                    : this.resolveViewOrder({ shelves, categories, order: this.filterDraft.order }).map((section) => section.label))
                : [];

            if (!name) {
                this.showFilterModalError('Give the view a name.', this.$filterName);
                return;
            }

            const lower = name.toLowerCase();
            if (this.filterViews.some((view) => view.id !== editingId && view.name.toLowerCase() === lower)) {
                this.showFilterModalError(`There's already a view called "${name}".`, this.$filterName);
                return;
            }

            if (!shelves.length && !categories.length) {
                this.showFilterModalError(isMosaic ? 'Pick at least one shelf.' : 'Pick at least one shelf or category.', this.$filterSearch);
                return;
            }

            if (editingId) {
                const view = this.filterViews.find((item) => item.id === editingId);
                if (view) {
                    view.name = name;
                    view.shelves = shelves;
                    view.categories = categories;
                    view.order = order;
                }
            } else {
                const id = `fv_${Date.now().toString(36)}${Math.random().toString(36).slice(2, 6)}`;
                this.filterViews.push({ id, name, shelves, categories, order });
                // A new view is applied right away so the result is visible
                this.activeFilterId = id;
            }

            // Saving a reordered view that's showing switches the table to its order
            if (!editingId || editingId === this.activeFilterId) {
                this.syncSortForActiveView(order.length > 0);
            }
            this.saveFilterViews();
            this.closeFilterModal();
            this.renderAllTopicsList();
            this.$listBody.scrollTop(0);
            this.measureNavHeight();
            this.fitListBody();
            if (!editingId) {
                this.$filterChips.find('.section-nav-parent-filter-chip--active .section-nav-parent-filter-chip-apply').trigger('focus');
            }
        }

        showFilterDeleteConfirm(show) {
            const $wrap = this.$filterModal.find('.yt-filter-modal-delete-wrap');
            $wrap.find('.yt-filter-modal-delete').toggle(!show);
            $wrap.find('.yt-filter-modal-confirm').prop('hidden', !show);
            // The confirm takes the whole footer so it doesn't wrap next to Cancel / Save
            this.$filterModal.find('.yt-filter-modal-footer').toggleClass('yt-filter-modal-footer--confirming', show);
            if (show) {
                const view = this.filterViews.find((item) => item.id === this.filterDraft.id);
                $wrap.find('.yt-filter-modal-confirm-text').text(view ? `Delete "${view.name}"?` : 'Delete this view?');
                $wrap.find('.yt-filter-modal-confirm-no').trigger('focus');
            }
        }

        deleteFilterView(id) {
            this.filterViews = this.filterViews.filter((view) => view.id !== id);
            if (this.activeFilterId === id) {
                this.activeFilterId = null;
            }
            this.syncSortForActiveView(false);
            this.saveFilterViews();
            this.filterModalOpener = this.$filterNew ? this.$filterNew[0] : null;
            this.closeFilterModal();
            this.renderAllTopicsList();
            this.measureNavHeight();
            this.fitListBody();
        }

        // On short screens the stuck nav can push the list past the bottom edge; cap it to what's visible
        fitListBody() {
            if (!this.$listBody || !this.$listBody.length) {
                return;
            }

            this.$listBody.css('max-height', '');
            if (this.$list.is('[hidden]') || !this.$nav.hasClass('is-stuck')) {
                return;
            }

            const bodyEl = this.$listBody[0];
            const cssMax = parseFloat(window.getComputedStyle(bodyEl).maxHeight) || Infinity;
            const available = window.innerHeight - Math.max(bodyEl.getBoundingClientRect().top, 0) - 8;
            if (available < cssMax) {
                this.$listBody.css('max-height', `${Math.max(available, 200)}px`);
            }
        }

        // Shelf sub-headers stick just under the column header
        syncTableHeadOffset() {
            const $table = this.$listGrid ? this.$listGrid.find('.section-nav-parent-table') : $();
            if (!$table.length) {
                return;
            }

            const headHeight = $table.find('thead').outerHeight();
            if (headHeight) {
                $table[0].style.setProperty('--section-nav-table-head-h', `${headHeight}px`);
            }
        }

        setListSort(key) {
            if (key === 'view') {
                this.listSortKey = 'view';
                this.listSortDir = 'asc';
            } else if (this.listSortKey === key) {
                this.listSortDir = this.listSortDir === 'desc' ? 'asc' : 'desc';
            } else {
                this.listSortKey = key;
                this.listSortDir = 'asc';
            }

            this.renderAllTopicsList();
            this.$listBody.scrollTop(0);
        }

        toggleListView() {
            this.listView = this.listView === 'alphabetical' ? 'mosaic' : 'alphabetical';
            localStorage.setItem('YT__all-topics-view', this.listView);
            this.loadFilterViews();
            this.syncSortForActiveView(true);
            this.updateViewToggleUi();
            this.renderAllTopicsList();
            this.$listBody.scrollTop(0);
            this.measureNavHeight();
        }

        updateViewToggleUi() {
            if (!this.$viewToggle || !this.$viewToggle.length) {
                return;
            }

            const isListView = this.listView === 'alphabetical';
            const $icon = this.$viewToggle.find('i');

            this.$list.toggleClass('section-nav-parent-list--alphabetical', isListView);

            if (this.$filterNew && this.$filterNew.length) {
                this.$filterNew.attr('title', isListView
                    ? 'Save a set of shelves or categories as a filter view'
                    : 'Save a set of shelves and their order as a mosaic view');
            }

            this.syncAlphaToggleUi();

            this.$viewToggle.attr(
                'aria-label',
                isListView ? 'Switch to mosaic view' : 'Switch to alphabetical list view'
            );
            this.$viewToggle.attr(
                'title',
                isListView ? 'Mosaic view' : 'Alphabetical list'
            );
            $icon.attr('class', isListView ? 'fa fa-th' : 'fa fa-list');
        }

        jumpToSection(index) {
            if (index < 0 || index >= this.sections.length) return;

            this.collapseList();
            this.measureNavHeight();

            this.suppressSpy = true;
            this.currentIndex = index;
            this.updateNav();

            const section = this.sections[index];
            const $iframe = $('#favs-wrapper iframe');
            const iframeTop = $iframe.offset().top;
            const elOffsetInIframe = $(section.el).offset().top - $iframe.contents().find('body').offset().top;
            const scrollY = iframeTop + elOffsetInIframe - this.navHeight - this.triggerBuffer;

            window.scrollTo({ top: scrollY, behavior: 'smooth' });

            setTimeout(() => {
                this.suppressSpy = false;
                this.syncCurrentSection();
            }, 250);
        }

        onScroll() {
            if (this.suppressSpy || this.rafPending) return;
            this.rafPending = true;
            requestAnimationFrame(() => {
                this.rafPending = false;
                this.updateStuckState();
                this.syncCurrentSection();
            });
        }

        onResize() {
            this.measureNavHeight();
            this.syncTableHeadOffset();
            this.fitListBody();
            this.updateStuckState();
            this.syncCurrentSection();
        }

        syncCurrentSection() {
            if (this.suppressSpy) return;
            if (this.sections.length === 0) return;

            const $iframe = $('#favs-wrapper iframe');
            if (!$iframe.length) return;

            const iframeTop = $iframe.offset().top;
            const triggerY = this.navHeight + this.triggerBuffer;
            let newIndex = 0;

            try {
                const iframeBodyTop = $iframe.contents().find('body').offset().top;
                
                for (let i = 0; i < this.sections.length; i++) {
                    const elOffsetInIframe = $(this.sections[i].el).offset().top - iframeBodyTop;
                    const elTop = iframeTop + elOffsetInIframe - window.scrollY;
                    if (elTop <= triggerY) {
                        newIndex = i;
                    } else {
                        break;
                    }
                }
            } catch(e) {
                return;
            }

            if (newIndex !== this.currentIndex) {
                this.currentIndex = newIndex;
                this.updateNav();
            }
        }

        fillLabeled($el, label, displayLabel) {
            $el.empty();
            const shown = displayLabel || label;
            if (!shown) return;
            const iconHtml = (typeof window.playlistIconHtml === "function") ? window.playlistIconHtml(label || shown) : "";
            if (iconHtml) $el.append(iconHtml);
            $el.append($('<span class="playlist-icon-label"></span>').text(shown));
        }

        updateNav() {
            const current = this.sections[this.currentIndex];
            const prev = this.sections[this.currentIndex - 1];
            const next = this.sections[this.currentIndex + 1];

            this.fillLabeled(this.$current, current ? current.label : '');

            if (prev) {
                this.$prevBtn.show();
                this.fillLabeled(this.$prevBtn.find('.section-nav-parent-label'), prev.label);
            } else {
                this.$prevBtn.hide();
            }

            if (next) {
                this.$nextBtn.show();
                this.fillLabeled(this.$nextBtn.find('.section-nav-parent-label'), next.label);
            } else {
                this.$nextBtn.hide();
            }

            this.$listGrid.find('.section-nav-parent-item').each((_, el) => {
                const $el = $(el);
                const sectionIndex = parseInt($el.attr('data-section-index'), 10);
                if (sectionIndex === this.currentIndex) {
                    $el.addClass('section-nav-parent-item--active');
                    $el.attr('aria-current', 'true');
                } else {
                    $el.removeClass('section-nav-parent-item--active');
                    $el.removeAttr('aria-current');
                }
                $el.closest('.section-nav-parent-table-row')
                    .toggleClass('section-nav-parent-table-row--active', sectionIndex === this.currentIndex);
            });

            this.$listGrid.find('.section-nav-parent-group').removeClass('section-nav-parent-group--active');
            const $activeItem = this.$listGrid.find('.section-nav-parent-item--active');
            if ($activeItem.length) {
                $activeItem.prevAll('.section-nav-parent-group').first().addClass('section-nav-parent-group--active');
            }
        }
    }

    window.parentSectionNav = new ParentSectionNav();
    $(function() {
        window.parentSectionNav.init();
    });
    </script>

    <div id="watch-history-root" class="watch-history-root" aria-hidden="true">
        <button type="button" class="watch-history-backdrop" id="watch-history-backdrop" aria-label="Close history"></button>
        <aside id="watch-history-panel" class="watch-history-panel" role="dialog" aria-modal="true" aria-labelledby="watch-history-title">
            <div class="watch-history-header">
                <h2 id="watch-history-title" class="watch-history-title">Recently watched</h2>
                <button type="button" class="watch-history-close" id="watch-history-close" aria-label="Close history">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="watch-history-body">
                <p id="watch-history-empty" class="watch-history-empty" hidden>No playlists watched yet.</p>
                <table id="watch-history-table" class="watch-history-table">
                    <thead>
                        <tr>
                            <th scope="col">Playlist</th>
                            <th scope="col" aria-sort="none">
                                <button type="button" class="watch-history-sort" data-sort="date">
                                    Date
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" aria-sort="none">
                                <button type="button" class="watch-history-sort" data-sort="minutes">
                                    Minutes
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" class="watch-history-delete-col"><span class="sr-only">Delete</span></th>
                        </tr>
                    </thead>
                    <tbody id="watch-history-rows"></tbody>
                </table>
            </div>
            <div class="watch-history-footer" id="watch-history-footer">
                <button type="button" class="watch-history-clear-all" id="watch-history-clear-all">Clear all</button>
                <label class="watch-history-advanced">
                    <input type="checkbox" id="watch-history-advanced">
                    <span class="watch-history-advanced-track" aria-hidden="true"></span>
                    <span class="watch-history-advanced-label">Advanced</span>
                </label>
            </div>
        </aside>
    </div>

    <script src="assets/js/app.js?v=5"></script>
    <div id="playlist-guide-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="playlist-guide-title">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="playlist-guide-title">Copy a playlist URL</h4>
                </div>
                <div class="modal-body" id="playlist-guide-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="openPlaylistIdFromGuide()">Playlist ID</button>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>
