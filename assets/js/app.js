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

// Shows the playlist collections panel inside the favs iframe.
// Used by the "Collections" button next to the heading (and the older triple-click shortcut).
function openFavCollectionsPanel() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var $modal = $("#favs-wrapper iframe").contents().find("#modal-more");
            $modal.find(".contents").html(this.responseText);
            $modal.removeClass("fade").attr("aria-hidden", "false");
            resizeIframeFavs();
        }
    };
    xhttp.open("GET", "list-favs.php?prependPath=" + encodeURIComponent(window.prependPath || "./"), true);
    xhttp.send();
}
