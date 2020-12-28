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

                        if (More.areas.slice(-4).join(",") === "Favheader,Favheader,Playlistheader,Playlistheader") {
                            More.count = 0;
                            More.areas = [];

                            var xhttp = new XMLHttpRequest();
                            xhttp.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    var responseText = this.responseText;
                                    $("#favs").contents().find("#modal-more").html(responseText);
                                }
                            };
                            xhttp.open("GET", "list-favs.php?prependPath=" + prependPath, true);
                            xhttp.send();


                            $("#favs").contents().find("#modal-more").removeClass("fade");
                        }


                    }
                } // check

                console.log(this.areas);
            } // count
    } // object More