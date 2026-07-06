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
            var count = $("li:visible").length;
            var randomPlaylist = parseInt(Math.random() * (count + 1));
            $("li:visible").eq(randomPlaylist).find("a").click();
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
    let lastOpened = localStorage.getItem("YT__last-opened");
    let arrLastOpened = [];
    if (lastOpened) {
        arrLastOpened = JSON.parse(lastOpened);
    }
    
    setTimeout(()=>{
        renderCrossedOutPlaylists(arrLastOpened);
    }, 1000);
})