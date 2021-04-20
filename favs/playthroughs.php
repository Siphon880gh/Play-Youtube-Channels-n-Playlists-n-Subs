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
  {id: 'PLs1-UdHIwbo497viX2cCYHUNVBsORkhbl', name: '[Entertainment/Videogames] Playthrough: Call of Duty WW2'},
{id: 'PL_sYhAj0WXROBlFPHkRJ2-AgOq8ndsPJK', name: '[Entertainment/Videogames] Playthrough: Chrono Trigger'},
{id: 'PL7RtZMiaOk8gRGz8Jr3DaLGcLo_Ai0_EE', name: '[Entertainment/Videogames] Playthrough: Demon\'s Souls Remake'},
{id: 'PLD14CA711E34ACAFD', name: '[Entertainment/Videogames] Playthrough: EarthBound'},
{id: 'PLs1-UdHIwbo4HiYSZ0ykpBhr86L8_AOL3', name: '[Entertainment/Videogames] Playthrough: Cyberpunk 2077'},
{id: 'PLR2vpfke_UnB6o0t0bpiZo3wuUg0ql1pK', name: '[Entertainment/Videogames] Playthrough: Half Life 1'},
{id: 'PLj_Goi54wf0d2xk_aLt-cFer_XTqI4qaN', name: '[Entertainment/Videogames] Playthrough: Half Life 2'},
{id: 'PLMBYlcH3smRzz8VqMEUO2i9Vu3ynWAkQ0', name: '[Entertainment/Videogames] Playthrough: God of War'},
{id: 'PLAnfG6CHIYjwTwqSJgWMFktc6Xo1VxJ8B', name: '[Entertainment/Videogames] Playthrough: Halo 2'},
{id: 'PLAnfG6CHIYjx8Da2Aw1q0hbhuKE3pbQYz', name: '[Entertainment/Videogames] Playthrough: Halo Combact Evolved'},
{id: 'PLs1-UdHIwbo6ayzHIHratdbJQ9i9XtrBh', name: '[Entertainment/Videogames] Playthrough: Immortals Fenyx Rising'},
{id: 'PL_sYhAj0WXRMFBAf-HK4XtmjWDNSKoPVI', name: '[Entertainment/Videogames] Playthrough: Kid Icarus Uprising'},
{id: 'PL8EC21DD302AEF995', name: '[Entertainment/Videogames] Playthrough: Paper Mario'},
{id: 'PLkR3RWLcKJUQxM_1P5fBuIzo51njAG1pV', name: '[Entertainment/Videogames] Playthrough: Paper Mario Color Splash'},
{id: 'PLkR3RWLcKJUTtbcVGfBfZi7Z4UvY7BZSv', name: '[Entertainment/Videogames] Playthrough: Paper Mario Sticker Star'},
{id: 'PLCD434C085CB7C8EA', name: '[Entertainment/Videogames] Playthrough: Paper Mario, Super'},
{id: 'PLF00C64B86BD75D79', name: '[Entertainment/Videogames] Playthrough: Paper Mario Thousand-Year Door'},
{id: 'UUYmGCAwhHmpV4LZrbvpjaTg', name: '[Entertainment/Videogames] Playthrough: Resident Evil 3 Remake ~Jill Mods'},
{id: 'PLkR3RWLcKJUQWf9JidznuOIvMWIBHmF2v', name: '[Entertainment/Videogames] Playthrough: Super Mario 64'},
{id: 'PLkR3RWLcKJUTBMe6blf5YMaU2DhdtF4Iv', name: '[Entertainment/Videogames] Playthrough: Super Mario Galaxy'},
{id: 'PL_sYhAj0WXROScbAOakRhAsINVoa9sSmh', name: '[Entertainment/Videogames] Playthrough: Super Mario Galaxy 2'},
{id: 'PL57hJfweW_2s1aLE9WtLCzKAAWWK0VJeH', name: '[Entertainment/Videogames] Playthrough: Super Mario RPG'},
{id: 'PLkR3RWLcKJUSMTYZ3NAXVCk4S3PflWaVf', name: '[Entertainment/Videogames] Playthrough: Super Mario Sunshine'},
{id: 'PL_sYhAj0WXRM5w3LkVEVGE_7dbvf4n41S', name: '[Entertainment/Videogames] Playthrough: Super Mario Superstar Saga'},
{id: 'PL-7t9DoIELCQPdyHZAy5xdfDXM6HDpEkv', name: '[Entertainment/Videogames] Playthrough: Super Mario Odyssey'},
{id: 'PLzg85AHZsA6aglhKWjYkDFci7FwrBpYJP', name: '[Entertainment/Videogames] Playthrough: Torchlight 2'},
{id: 'PL_sYhAj0WXRM2_xfO1O6P040gwIwlhqcP', name: '[Entertainment/Videogames] Playthrough: Xenoblade Chronicles'},
{id: 'PL_sYhAj0WXRPQf_RR-km-i34wPrQrhncN', name: '[Entertainment/Videogames] Playthrough: Xenoblade Chronicles 2'},
{id: 'PLzg85AHZsA6YbtfGkRpvwToPqZe9ZzQF_', name: '[Entertainment/Videogames] Playthrough: Zelda ~Romhacks'},
{id: 'PL-7t9DoIELCTaNipmD-W5tJ727C7c1z0N', name: '[Entertainment/Videogames] Playthrough: Zelda Hyrule Warriors 2014'},
{id: 'PL-7t9DoIELCSzP5rT-nmxOr5uvsgkG4ss', name: '[Entertainment/Videogames] Playthrough: Zelda Hyrule Warriors: Age of Calamity'},
{id: 'PL_sYhAj0WXRMHL_fIWlbHhyxh8d2GhM_r', name: '[Entertainment/Videogames] Playthrough: Zelda A Link to the Past'},
{id: 'PLFdQ3YMJDN9jbWIFvmjgvolBLieiUkP2o', name: '[Entertainment/Videogames] Playthrough: Zelda Breath of the Wild'},
{id: 'PLDgypNs3MY2RVI1ZpZX0tDE_JW3q3zgMT', name: '[Entertainment/Videogames] Playthrough: Zelda Breath of the Wild (No Commentary)'},
{id: 'PLEE458B6CFC6D6035', name: '[Entertainment/Videogames] Playthrough: Zelda Ocarina of Time'},
{id: 'PLF41D831CF4427BE5', name: '[Entertainment/Videogames] Playthrough: Zelda Majora\'s Mask'},
{id: 'PL_sYhAj0WXRNkqeo_T2DJ-pOVVlgzt_GW', name: '[Entertainment/Videogames] Playthrough: Zelda Phantom Hourglass '},
{id: 'PL_sYhAj0WXRM_YCtn-d-RAh__2lNr-5ny', name: '[Entertainment/Videogames] Playthrough: Zelda Skyward Sword '},
{id: 'PL_sYhAj0WXRPVCwyLyphrwVDdqhhSyC9n', name: '[Entertainment/Videogames] Playthrough: Zelda Tri-Force Heroes'},
{id: 'PLFdQ3YMJDN9itelE3ZIIpsP0lPCuUWxtC', name: '[Entertainment/Videogames] Playthrough: Zelda Twilight Princess'},
{id: 'PL_lABM4PHKBKFfw25CMPWmsQYKtT2wDzx', name: '[Entertainment/Videogames] Playthrough: Zelda Wind Waker'},
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