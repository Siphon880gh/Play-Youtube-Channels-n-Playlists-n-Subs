<script>window.parent.prependPath="./";</script>
<?php $prependPath="./"; ?>
<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href="<?php echo $prependPath; ?>assets/css/favs.css" rel="stylesheet"></link>
<script src="<?php echo $prependPath; ?>assets/js/favs.js"></script>
<?php include($prependPath."assets/templates/favs.php"); ?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
<div id="favs">
  <div class="header">
    <?php
    /**
     * @events
     *
     * Secret gesture on "Watch videos from Youtube channels"
     * and "Playlist" headers that open more Fav collections.
     * In respective order, the combination is
     * click x2, then click x2.
     * 
    */
    ?>
    <a href="javascript:void(0);" onclick="window.parent.More.counter('Favheader');">Watch videos from Youtube channels:</a>
    <div class="float-right float-right-buttons" style="padding-right: 5px;">
      <i id="random" class="fa fa-random clickable" onclick="RandomPlaylist.select();" style="margin-left:3px;"></i>
      <div style="width:1px; height:10px;"></div>
      <i id="manual" class="fa fa-cloud-upload-alt clickable" onclick="ManualPlaylist.prompt();"></i>
    </div> <!-- /float-right -->
  </div> <!-- /header -->
  <ul class="playlists-target">
  </ul>
</div>
<script>
var params = new URLSearchParams(window.location.search);
var currentCollection = params.get("favs") || "default";
if(currentCollection==="favs") currentCollection = "default";


function changeVideo(event) {
    event.preventDefault();
    var id = $(event.target).attr("data-playlist-id") ;
    window.parent.urlChange.playlist(event, id);
  }

$(document).ready(function(){
  function renderPlaylists(collection) {
    var $playlistContainer = $(".playlists-target");

    for(var i =0; i<collection.length; i++) {
        var playlistObject = collection[i];
        var id = playlistObject.id;
        var name = playlistObject.name;

        var li = $("<li></li>")
        var a = $("<a></a>");

        // Add interactivity
        a.attr("href","javascript:void(0);");
        a.attr("data-playlist-id",id);
        a.attr("onclick", "changeVideo(event)");
        
        // Change text
        a.html(name);

        // Append a into li
        li.append(a);

        // Append to playlist list
        $playlistContainer.append(li);
      } // for

      resizeIframeNow();
  } // renderPlaylists

  function resizeIframeNow() {
    window.parent.resizeIframeFavs();
  }

  $.get("./favs/" + currentCollection + ".json", function(data, status){
    if(data) {
      collection = data;
      renderPlaylists(collection);
    }
  });

});

</script>