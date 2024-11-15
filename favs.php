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
    <a href="javascript:void(0);" onclick="fetchPlaylist('Default'); window.parent.More.counter('Favheader');">Youtube uploads and playlists:</a>
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
    <div style="border-bottom:1px solid rgba(125,125,125,.7); border-radius:0; display:inline-block; padding: 5px 10px; padding-bottom: 15px;">

      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Mood OR NLP") { $("#playlist-filter").val("Mood OR NLP") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Mood OR NLP]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="NLP") { $("#playlist-filter").val("NLP") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[NLP]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Travel") { $("#playlist-filter").val("Travel") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Travel]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='RandomPlaylist.select();'>[Rand]</a>

      <div style="width:1px; height:5px;"></div>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Comedy") { $("#playlist-filter").val("Comedy") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Comedy/Wake]</a>
      <span>|</span>
      <a target="_blank" href="//docs.google.com/spreadsheets/d/12KOsTfkDUbC-eJSxzKCKn1MGp0o4vSE65wNBd_FI_jg/edit#gid=1451394616">[Motiv]</a>
      <span>|</span>
      <a target="_blank" href="//wengindustry.com/tools/daily-portal/#analysis">[Paralysis]</a>
      <span>|</span>
      <a target="_blank" href="//wengindustry.com/tools/daily-portal/#impaired">[Impaired]</a>

      <div style="width:1px; height:5px;"></div>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Loopable") { $("#playlist-filter").val("Loopable") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[LL]</a>
      <span>|</span>
      <a target="_blank" href="//www.youtube.com/watch?v=q1YVvndNyqM">[EMDR]</a>
      <span>|</span>
      <a target="_blank" href="//www.youtube.com/watch?v=nMfPqeZjc2c">[White Noise Background]</a>

    </div>

    <div style="width:1px; height:10px;"></div>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="👁️") { $("#playlist-filter").val("👁️") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");' style="text-decoration:none;">👁️</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="👀") { $("#playlist-filter").val("👀") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");' style="text-decoration:none;">👀</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="👂") { $("#playlist-filter").val("👂") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");' style="text-decoration:none;">👂</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="❤️") { $("#playlist-filter").val("❤️") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");' style="text-decoration:none;">❤️</a>

    <div style="width:1px; height:5px;"></div>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Nelk") { $("#playlist-filter").val("Nelk") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Nelk]</a>
    <!-- <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Propa") { $("#playlist-filter").val("Propa") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Propa]</a> -->
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Reddit") { $("#playlist-filter").val("Reddit") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Reddit]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Zelda") { $("#playlist-filter").val("Zelda") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Zelda]</a>
    <span>&nbsp;</span>

    <div style="width:1px; height:5px;"></div>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="News") { $("#playlist-filter").val("News") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[News]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Opinion") { $("#playlist-filter").val("Opinion") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Opinion]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Review") { $("#playlist-filter").val("Review") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Review]</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Trailer") { $("#playlist-filter").val("Trailer") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Trailer]</a>
    <span>&nbsp;</span>
    
    <div style='margin-top:25px'></div>
    <a href="javascript:void(0)" onclick='$("#playlist-filter").val("").trigger("input");'><i class="fa fa-eraser" style="color:rgba(122,0,0);"></i></a>
    <span>&nbsp;</span>
    <label for="playlist-filter" onclick='$("#playlist-filter").val(""); filterListItems("", $(".playlists-target")); saveFilteredForRefresh("");'>Filter:</label>
    <input id="playlist-filter" class="playlist-filter" type="text" oninput="filterListItems($(event.target).val(), $('.playlists-target')); saveFilteredForRefresh($(event.target).val());" autocomplete="on" placeholder="term OR term OR...">
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
  let $allListItems = listItems.find("li");

  // Filtering vs reset to showing all list items
  if(userFilterText.length) {

    $allListItems.hide();

    var $filteredIn = null;
    var terms = userFilterText.split("OR").map(term => term.trim())
    terms.forEach(term=>{
      if(term.length===0) return true; // eg. A OR

      if($filteredIn===null) {
        $filteredIn = listItems.find(`li:contains('${term}')`);
      } else {
        $.merge($filteredIn, listItems.find(`li:contains('${term}')`));
      }
    }) // forEach
    $filteredIn.show();
  } else {
    $allListItems.show();
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
    var isPlaylistWantsShuffle = $el.text().toLowerCase().includes("(shuffle)") || $el.text().toLowerCase().includes("(shuffled)") // true or false
    console.log({isPlaylistWantsShuffle});
    if(isPlaylistWantsShuffle) {
      localStorage.setItem("YT__playlistWantsShuffle", "1");
    }
    window.parent.urlChange.playlist($el, id);
  }

// Fetch and render list of playlists

function fetchPlaylist(calledCollection) {
    if(calledCollection)
      currentCollection = calledCollection;

    // debugger;

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
  } // fetchPlaylist
  function renderPlaylists(collection) {
    var $playlistContainer = $(".playlists-target");
    $playlistContainer.html("");

    for(var i =0; i<collection.length; i++) {
        var playlistObject = collection[i];
        var id = (typeof playlistObject.id!=="undefined")?playlistObject.id:-1;
        var name = playlistObject.name;
        // console.log({id,name})

        var li = $("<li></li>")
        var a = $("<a></a>");

        // Add interactivity
        a.attr("href","javascript:void(0);");
        if(id===-1) {
          a.addClass('not-link') // dependency out there
          li.addClass('not-link') // dependency out there
        } else if(id) {
          a.attr("data-playlist-id",id);
          a.attr("onclick", "changeVideo(event)");
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

$(document).ready(function(){
  fetchPlaylist();
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