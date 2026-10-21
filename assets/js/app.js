var More = {
        areas: [],
        count: 0,

        counter: function(area) {
                console.log("count")
                this.areas.push(area)
                this.count++;

                // Clicking areas have a threshold of 1 second, otherwise dont count
                setTimeout(check, 500);

                function check() {
                    this.count--;
                    if (this.count <= 0) {
                        this.areas = [];
                    } else {

                        // let secretCombo = "Favheader,Favheader,Playlistheader";
                        let secretCombo = "Favheader,Favheader,Favheader";
                        if (More.areas.slice(-4).join(",") === secretCombo) {
                            More.count = 0;
                            More.areas = [];

                            openFavCollectionsPanel();
                        }


                    }
                } // check

                console.log(this.areas);
            } // count
    } // object More

// Collections panel lives in the sticky section bar, so it stays put when that bar sticks.
function openFavCollectionsPanel() {
    var panel = document.getElementById("fav-collections-panel");
    if (!panel) return;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            panel.querySelector(".contents").innerHTML = this.responseText;
            panel.hidden = false;
            panel.setAttribute("aria-hidden", "false");
            $(".section-nav-collections-btn").attr("aria-expanded", "true");
            if (window.parentSectionNav) window.parentSectionNav.measureNavHeight();
        }
    };
    xhttp.open("GET", "list-favs.php?prependPath=" + encodeURIComponent(window.prependPath || "./"), true);
    xhttp.send();
}

function closeFavCollectionsPanel() {
    var panel = document.getElementById("fav-collections-panel");
    if (!panel) return;
    panel.hidden = true;
    panel.setAttribute("aria-hidden", "true");
    $(".section-nav-collections-btn").attr("aria-expanded", "false");
    if (window.parentSectionNav) window.parentSectionNav.measureNavHeight();
}

function promptPrivateUnlock() {
    var panel = document.getElementById("fav-collections-panel");
    if (!panel) return;
    var contents = panel.querySelector(".contents");
    var existing = contents.querySelector(".private-unlock-form");
    if (existing) existing.remove();
    contents.insertBefore(buildCollectionsUnlockForm("Enter the password to see private collections.", "", function() {
        openFavCollectionsPanel();
    }), contents.firstChild);
    var input = contents.querySelector(".private-unlock-input");
    if (input) input.focus();
    if (window.parentSectionNav) window.parentSectionNav.measureNavHeight();
}

function lockPrivateCollections() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState !== 4) return;
        closeFavCollectionsPanel();
        var frame = document.querySelector("#favs-wrapper iframe");
        var win = frame && frame.contentWindow;
        if (win && typeof win.isPrivateCollection === "function" && win.isPrivateCollection(win.currentCollection) && typeof win.renderPrivateUnlockForm === "function") {
            win.renderPrivateUnlockForm(win.currentCollection, "Locked.");
        }
    };
    xhttp.open("POST", "private-favs.php?action=logout", true);
    xhttp.send();
}

function buildCollectionsUnlockForm(titleText, message, onUnlocked) {
    var form = document.createElement("form");
    form.className = "private-unlock-form";
    form.setAttribute("autocomplete", "on");
    form.innerHTML = '<p class="private-unlock-title"><i class="fa fa-lock" aria-hidden="true"></i> ' + titleText + '</p>'
        + '<input type="text" name="username" autocomplete="username" value="private-playlists" hidden>'
        + '<input type="password" name="password" class="private-unlock-input" autocomplete="current-password" placeholder="Password" required>'
        + '<button type="submit" class="private-unlock-btn">Unlock</button>'
        + '<p class="private-unlock-error" role="alert"></p>';
    form.querySelector(".private-unlock-error").textContent = message || "";
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        var button = form.querySelector(".private-unlock-btn");
        var input = form.querySelector(".private-unlock-input");
        var msg = form.querySelector(".private-unlock-error");
        button.disabled = true;
        msg.textContent = "";
        var body = new FormData();
        body.append("password", input.value);
        fetch("private-favs.php?action=login", { method: "POST", body: body, credentials: "same-origin" })
            .then(function(res) {
                if (!res.ok) throw res;
                input.value = "";
                onUnlocked();
            })
            .catch(function(res) {
                button.disabled = false;
                input.value = "";
                input.focus();
                if (res && res.status === 429) {
                    msg.textContent = "Too many wrong passwords. Try again in a minute.";
                } else if (res && res.status === 503) {
                    msg.textContent = "Private collections are not set up on the server yet.";
                } else {
                    msg.textContent = "Wrong password.";
                }
            });
    });
    return form;
}

$(document).on("click", "#fav-collections-panel a.fav-collections-link", function(event) {
    if (this.getAttribute("data-locked") === "1") return;
    var href = this.getAttribute("href") || "";
    if (!href || href.indexOf("javascript:") === 0) return;
    event.preventDefault();
    var iframe = document.querySelector("#favs-wrapper iframe");
    if (iframe) iframe.src = href;
    closeFavCollectionsPanel();
});
