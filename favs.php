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
    <a href="javascript:void(0);" onclick="window.parent.More.counter('Favheader');">Youtube uploads and playlists:</a>
    <div class="float-right float-right-buttons" style="padding-right: 5px;">
      <i id="random" class="fa fa-random clickable" onclick="RandomPlaylist.select();" style="margin-left:3px;"></i>
      <div style="width:1px; height:10px;"></div>
      <i id="manual" class="fa fa-cloud-upload-alt clickable" onclick="ManualPlaylist.prompt();"></i>
    </div> <!-- /float-right -->

    <div style="float:right; font-size: 1rem;">
      <a href="javascript:void(0)" onclick='localStorage.setItem("YT__last-opened", "[]"); $(".crossed-out").removeClass("crossed-out");'>Clear history</a>
    </div>
  </div> <!-- /header -->

  <div style="text-align: center; margin-top:20px;">
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="❤️") { $("#playlist-filter").val("❤️") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>❤️</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Nelk") { $("#playlist-filter").val("Nelk") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Nelk]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Propa") { $("#playlist-filter").val("Propa") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Propa]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Reddit") { $("#playlist-filter").val("Reddit") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Reddit]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Zelda") { $("#playlist-filter").val("Zelda") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Zelda]</a>
    <span>&nbsp;</span>

    <div style="width:1px; height:5px;"></div>
    <span style="border:1px solid gray; border-radius:5px;">
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Mood") { $("#playlist-filter").val("Mood") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Mood]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Travel") { $("#playlist-filter").val("Travel") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Travel]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='RandomPlaylist.select();'>[Rand]</a>
    </span>
    <span>&nbsp;</span>

    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="News") { $("#playlist-filter").val("News") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[News]</a>
    <span>&nbsp;</span>
    <div style="width:1px; height:5px;"></div>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Opinion") { $("#playlist-filter").val("Opinion") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Opinion]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Review") { $("#playlist-filter").val("Review") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Review]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Trailer") { $("#playlist-filter").val("Trailer") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Trailer]</a>
    <span>&nbsp;</span>
    <br/><br/>
    <a href="javascript:void(0)" onclick='$("#playlist-filter").val("").trigger("input");'><i class="fa fa-eraser"></i></a>
    <span>&nbsp;</span>
    <label for="playlist-filter" onclick='$("#playlist-filter").val(""); filterListItems("", $(".playlists-target")); saveFilteredForRefresh("");'>Filter:</label>
    <input id="playlist-filter" class="playlist-filter" type="text" oninput="filterListItems($(event.target).val(), $('.playlists-target')); saveFilteredForRefresh($(event.target).val());" autocomplete="on">
  </div>

  <ul class="playlists-target">
  </ul>
</div>
<script>
// case insensitive jquery contains
jQuery.expr[':'].contains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};

var params = new URLSearchParams(window.location.search);
var currentCollection = params.get("favs") || "Default";
if(currentCollection==="favs") currentCollection = "Default";

function filterListItems(userFilterText, listItems) {
  var allListItems = listItems.find("li");

  // Filtering vs reset to showing all list items
  if(userFilterText.length) {

    allListItems.hide();
    var filteredIn = listItems.find(`li:contains('${userFilterText}')`);
    filteredIn.show();
  } else {
    allListItems.show();
  }
} // filterListItems

function saveFilteredForRefresh(userFilterText) {
  localStorage.setItem("YT__last-filtered", userFilterText);
} // saveFilteredForRefresh

function changeVideo(event) {
    event.preventDefault();
    var isChildHTMLAndNotLink = $(event.target)[0].tagName.toLowerCase()!=="a";
    var $el = isChildHTMLAndNotLink?$(event.target).closest("a"):$(event.target);
    var id = $el.attr("data-playlist-id");
    window.parent.urlChange.playlist($el, id);
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
        if(id) {
          a.attr("data-playlist-id",id);
          a.attr("onclick", "changeVideo(event)");
        } else {
          a.addClass('not-link')
          li.addClass('not-link')
        }
        // Change text
        a.html(name);

        // Append a into li
        li.append(a);

        // Append to playlist list
        $playlistContainer.append(li);
      } // for


      // Continuing the filtered search input
      let lastFiltered = localStorage.getItem("YT__last-filtered");
      if(lastFiltered && lastFiltered.length) {
          let $filterInput = $("#favs").contents().find("#playlist-filter");
          $filterInput.val(lastFiltered).trigger("input");
      }

      resizeIframeNow();
  } // renderPlaylists

  function resizeIframeNow() {
    window.parent.resizeIframeFavs();
  }

  $.ajax({
      url: "./favs/" + currentCollection + ".json",
      method:"GET",
      cache: false,
      success: function(data, status) {
        if(data) {
          window.collection = data;
          renderPlaylists(window.collection);
        }
      }
  });

});

</script>
<style>
  a.not-link {
    color: gray !important;
    text-decoration: none;
    cursor: default;
  }
  li.not-link {
    list-style-type: none;
    margin-bottom: 5px;
  }
</style>