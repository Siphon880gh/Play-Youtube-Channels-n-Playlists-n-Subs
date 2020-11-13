class More { // static
    mouseIsDown = false;

    constructor() {
        if (typeof window.count === 'undefined') window.count = -1;
    }
    start() {
        if (window.count >= 2) {
            this.mouseIsDown = true;
            setTimeout(() => {
                if (this.mouseIsDown)
                    this.reached();
            }, 2000);
        }
    }
    stop() {
        this.mouseIsDown = false;
    }
    count() {
        window.count++;
        setTimeout(() => {
            window.count = -1;
        }, 3000);
    }
    reached() {
        window.count = -1;

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // document.getElementById("demo").innerHTML = this.responseText;
                var responseText = this.responseText;
                document.querySelector("#modal-more .contents").innerHTML = responseText;
            }
        };
        xhttp.open("GET", prependPath + "list-favs.php?prependPath=" + prependPath, true);
        xhttp.send();


        document.querySelector("#modal-more").classList.remove("fade");
    }
}

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