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

$(() => {
    // The last 10 opened playlists

    // Get last 10
    let lastOpened = localStorage.getItem("YT__last-opened");
    let arrLastOpened = [];
    if (lastOpened) {
        arrLastOpened = JSON.parse(lastOpened);
    }

    // Cross out the last 10
    arrLastOpened.forEach(itrPlaylistName => {
        $(`li a:contains('${itrPlaylistName}')`).addClass("crossed-out");
    });
})