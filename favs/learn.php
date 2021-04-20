<script>window.parent.prependPath="../";</script>
<?php $prependPath="../"; ?>
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

var playlistList = [
  {id: 'UUSH9YxXcGCaKNh1JpUy9sMg', name: '[Cooking] GrubMarket'},
  {id: 'UU8gFadPgK2r1ndqLI04Xvvw', name: '[Cooking/Korean] Maangchi'},
  {id: 'UUQMyhrt92_8XM0KgZH6VnRg', name: '[Curiosity/business] Company Man'},
  {id: 'UU_yjveGdyx6mqqHkHaD-_bg', name: '[Curiosity/medical] Larry Mellick (ER procedures)'},
  {id: 'PL2Fa-9lE-_PwvJECVPzyhTJCFq5nqOJHi', name: '[Curiosity/medical] ICU Documentary'},
  {id: 'PL2Fa-9lE-UUKcWSiffY8MpZ3NKav8LeRA', name: '[Curiosity/nutrition] Motivational Doc'},
  {id: 'UUeZLO2VgbZHeDcongKzzfOw', name: '[Curiosity/music] 8-bit Music Theory'},
  {id: 'UUnkp4xDOwqqJD7sSM3xdUiQ', name: '[Curiosity/music] Adam Neely'},
  {id: 'UUUyeh8X37t_xJ77M6gHb1PQ', name: '[Curiosity/music] MangoldProject'},
  {id: 'UUyy7dZhgfeMMctSoo3wDXlQ', name: '[Curiosity/music] Thomas Game Docs (Occasional videogame music theory)'},
  {id: 'PLzg85AHZsA6ZiXxYJant9PXtY9AB4aaJ4', name: '[Curiosity/music] Music Theory'},
  {id: 'UUC552Sd-3nyi_tk2BudLUzA', name: '[Curiosity/science] AsapScience'},
  {id: 'UUHYoe8kQ-7Gn9ASOlmI0k6Q', name: '[Curiosity/science] Food Theorist'},
  {id: 'UUeTTmfuH2CbbBHWLiyrNsuQ', name: '[Education/medical] AllNursingNotes'},
  {id: 'PLzg85AHZsA6b9rFWGl5vOwkG5m6imCkwX', name: '[Education/medical] CCRN Disks'},
  {id: 'UUMSsLqxqvZsNXi0Z-VjN89A', name: '[Education/medical] Doctor Mike Hansen'},
  {id: 'UUOKni4MYHRT45UgyMM2f0QQ', name: '[Education/medical] MaiMaryRN'},
  {id: 'UU6QYFutt9cluQ3uSM963_KQ', name: '[Education/medical] Ninja Nerd Lectures'},
  {id: 'UUUxQWmWk1_Hk9iDRKvhH29Q', name: '[Education/medical] Simple Nursing'},
  {id: 'UUKOvOaJv4GK-oDqx-sj7VVg', name: '[Education/medical] Chubbyemu'},
  {id: 'UUyjFUnWxd9ymR9Z8QXAlV3g', name: '[Education/medical] Dr Adeleke'},
  {id: 'UUuDh74_iAumVGF8AM4FJoUw', name: '[Education/medical] MainMD'},
  {id: 'UUG-iSMVtWbbwDDXgXXypARQ', name: '[Education/medical] MedCram'},
  {id: 'UUjMCFF9O02YXlJ1MYll3k0Q', name: '[Education/medical] Pharmacy'},
  {id: 'PLzg85AHZsA6ZadXv0lxlUc-Ru89BzObZX', name: '[Fitness] ~Bodybuilding'},
  {id: 'PLzg85AHZsA6YV7yaU-Uk7Sl_od8dC5jS0', name: '[Fitness] ~Design'},
  {id: 'PLzg85AHZsA6aYMCXUdkUqkzaOy73IyAhp', name: '[Fitness] ~Food Prep'},
  {id: 'PLzg85AHZsA6bdXPppP_X4ohADNCzeMKj8', name: '[Fitness/Flexibility] Bob & Brad'},
  {id: 'UUyA6KQ8PgP98E0ToIdq8c7A', name: '[Fitness/Flexibility] Chiropractor Dr. Alex Tubio'},
  {id: 'UURLOLGZl3-QTaJfLmAKgoAw', name: '[Fitness] Alan Thrall'},
  {id: 'UUe0TLA0EsQbE-MjuHXevj2A', name: '[Fitness] Athlean'},
  {id: 'UU7aE5B-ZFEAoumyj6FaJ7lg', name: '[Fitness] Bradley Martyn'},
  {id: 'UUduKuJToxWPizJ7I2E6n1kA', name: '[Fitness] BroScienceLife'},
  {id: 'UUKf0UqBiCQI4Ol0To9V0pKQ', name: '[Fitness] Buff Dudes'},
  {id: 'UU_7lEuEKvFt63jtvZYwlHMQ', name: '[Fitness] Eugene Teo'},
  {id: 'UUg_tz2iw7_-gf2xIL-DdM2g', name: '[Fitness] Kali Muscle'},
  {id: 'UULqH-U2TXzj1h7lyYQZLNQQ', name: '[Fitness] Greg Doucette'},
  {id: 'UU68TLK0mAEzUyHx5x5k-S1Q', name: '[Fitness] Jeff Nippard'},
  {id: 'UU1XHNZDn3btv7454Pkz7THg', name: '[Fitness] Scooby1961'},
  {id: 'UUXrqErU_TjqiHAHJkzITAvg', name: '[Fitness] Shredded Sports Science'},
  {id: 'UUCCbzhtMMAxjSBmfRvd-7yw', name: '[Fitness] Shytown Fitness'},
  {id: 'UU0ASolYU_Yh3yShLFQC0stg', name: '[Fitness] Strength Camp'},
  {id: 'UUqjwF8rxRsotnojGl4gM0Zw', name: '[Fitness] ThenX'},
  {id: 'UUwFplYvhHks5TV3bWxUrU9A', name: '[Fitness/Physical.Therapy] Back Intelligence'},
  {id: 'UUmTe0LsfEbpkDpgrxKAWbRA', name: '[Fitness/Physical.Therapy] Bob & Brad'},
  {id: 'PLzg85AHZsA6YF7gunvezRd6wB5loe_giG', name: '[GF] Relationships'},
  {id: 'PL7267CDBCFA3172CE', name: '[GF] Relationships - Body Language'},
  {id: 'UUtQEjfEFzba37ozoXK40r3w', name: '[GF] Relationships - How to Beast'},
  {id: 'UUO1cgjhGzsSYb1rsB4bFe4Q', name: '[Programming/JS] Fun Fun Function'},
  {id: 'UUsLo154Krjwhoz8W00N8ItA', name: '[Programming/JS] Keep On Coding'},
  {id: 'UUTBGXCJHORQjivtgtMsmkAQ', name: '[Programming/JS] Steve Griffith'},
];

$(document).ready(function(){
  function changeVideo(event) {
    event.preventDefault();
    var id = $(event.target).attr("data-playlist-id") ;
    window.parent.urlChange.playlist(event, id);
  }
  var $playlistContainer = $(".playlists-target");

  for(var i =0; i<playlistList.length; i++) {
    var playlistObject = playlistList[i];
    var id = playlistObject.id;
    var name = playlistObject.name;

    var li = $("<li></li>")
    var a = $("<a></a>");

    // Add interactivity
    a.attr("href","javascript:void(0);");
    a.attr("data-playlist-id",id);
    a.click(changeVideo);
    
    // Change text
    a.text(name);

    // Append a into li
    li.append(a);

    // Append to playlist list
    $playlistContainer.append(li);
  }

  window.parent.resizeIframeFavs();
});
</script>