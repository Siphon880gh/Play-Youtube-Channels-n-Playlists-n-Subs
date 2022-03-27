class ManualPlaylist {
    constructoor() {}
    prompt() {
            $(window.parent.document).find("#modal-override-playlist-id").modal("show");
        } // prompt
}
ManualPlaylist = new ManualPlaylist();

class RandomPlaylist {
    constructor() {}
    select() {
            var count = $("li").length;
            var randomPlaylist = parseInt(Math.random() * (count + 1));
            $("li").eq(randomPlaylist).find("a").click();
        } // select
} // RandomPlaylist
RandomPlaylist = new RandomPlaylist();

window.renderCrossedOutPlaylists = function(arrLastOpened) {
    $("#favs-wrapper iframe").contents().find("a.crossed-out").removeClass("crossed-out");

    arrLastOpened.forEach(playlistId => {
        console.log("Last seen playlist id: " + playlistId);
        $("#favs-wrapper iframe").contents().find(`[data-playlist-id="${playlistId}"]`).addClass("crossed-out");
    });
}
$(() => {
    // Main: Render cross out for the last 10 opened playlists

    // Sub: Get last 10
    let lastOpened = localStorage.getItem("YT__last-opened");
    let arrLastOpened = [];
    if (lastOpened) {
        arrLastOpened = JSON.parse(lastOpened);
    }
    
    // Sub: Cross out the last 10
    setTimeout(()=>{
        renderCrossedOutPlaylists(arrLastOpened);
    }, 1000);
})