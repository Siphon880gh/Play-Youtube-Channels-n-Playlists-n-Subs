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
  <ul>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLs1-UdHIwbo497viX2cCYHUNVBsORkhbl'>[Entertainment\Videogames] Playthrough: Call of Duty WW2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXROBlFPHkRJ2-AgOq8ndsPJK'>[Entertainment\Videogames] Playthrough: Chrono Trigger</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL7RtZMiaOk8gRGz8Jr3DaLGcLo_Ai0_EE'>[Entertainment\Videogames] Playthrough: Demon's Souls Remake</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLD14CA711E34ACAFD'>[Entertainment\Videogames] Playthrough: EarthBound</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLs1-UdHIwbo4HiYSZ0ykpBhr86L8_AOL3'>[Entertainment\Videogames] Playthrough: Cyberpunk 2077</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLR2vpfke_UnB6o0t0bpiZo3wuUg0ql1pK'>[Entertainment\Videogames] Playthrough: Half Life 1</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLj_Goi54wf0d2xk_aLt-cFer_XTqI4qaN'>[Entertainment\Videogames] Playthrough: Half Life 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLMBYlcH3smRzz8VqMEUO2i9Vu3ynWAkQ0'>[Entertainment\Videogames] Playthrough: God of War</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLAnfG6CHIYjwTwqSJgWMFktc6Xo1VxJ8B'>[Entertainment\Videogames] Playthrough: Halo 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLAnfG6CHIYjx8Da2Aw1q0hbhuKE3pbQYz'>[Entertainment\Videogames] Playthrough: Halo Combact Evolved</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLs1-UdHIwbo6ayzHIHratdbJQ9i9XtrBh'>[Entertainment\Videogames] Playthrough: Immortals Fenyx Rising</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRMFBAf-HK4XtmjWDNSKoPVI'>[Entertainment\Videogames] Playthrough: Kid Icarus Uprising</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL8EC21DD302AEF995'>[Entertainment\Videogames] Playthrough: Paper Mario</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUQxM_1P5fBuIzo51njAG1pV'>[Entertainment\Videogames] Playthrough: Paper Mario Color Splash</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUTtbcVGfBfZi7Z4UvY7BZSv'>[Entertainment\Videogames] Playthrough: Paper Mario Sticker Star</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLCD434C085CB7C8EA'>[Entertainment\Videogames] Playthrough: Paper Mario, Super</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLF00C64B86BD75D79'>[Entertainment\Videogames] Playthrough: Paper Mario Thousand-Year Door</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUYmGCAwhHmpV4LZrbvpjaTg'>[Entertainment\Videogames] Playthrough: Resident Evil 3 Remake ~Jill Mods</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUQWf9JidznuOIvMWIBHmF2v'>[Entertainment\Videogames] Playthrough: Super Mario 64</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUTBMe6blf5YMaU2DhdtF4Iv'>[Entertainment\Videogames] Playthrough: Super Mario Galaxy</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXROScbAOakRhAsINVoa9sSmh'>[Entertainment\Videogames] Playthrough: Super Mario Galaxy 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL57hJfweW_2s1aLE9WtLCzKAAWWK0VJeH'>[Entertainment\Videogames] Playthrough: Super Mario RPG</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUSMTYZ3NAXVCk4S3PflWaVf'>[Entertainment\Videogames] Playthrough: Super Mario Sunshine</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRM5w3LkVEVGE_7dbvf4n41S'>[Entertainment\Videogames] Playthrough: Super Mario Superstar Saga</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL-7t9DoIELCQPdyHZAy5xdfDXM6HDpEkv'>[Entertainment\Videogames] Playthrough: Super Mario Odyssey</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aglhKWjYkDFci7FwrBpYJP'>[Entertainment\Videogames] Playthrough: Torchlight 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRM2_xfO1O6P040gwIwlhqcP'>[Entertainment\Videogames] Playthrough: Xenoblade Chronicles</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRPQf_RR-km-i34wPrQrhncN'>[Entertainment\Videogames] Playthrough: Xenoblade Chronicles 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6YbtfGkRpvwToPqZe9ZzQF_'>[Entertainment\Videogames] Playthrough: Zelda ~Romhacks</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL-7t9DoIELCTaNipmD-W5tJ727C7c1z0N'>[Entertainment\Videogames] Playthrough: Zelda Hyrule Warriors 2014</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL-7t9DoIELCSzP5rT-nmxOr5uvsgkG4ss'>[Entertainment\Videogames] Playthrough: Zelda Hyrule Warriors: Age of Calamity</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRMHL_fIWlbHhyxh8d2GhM_r'>[Entertainment\Videogames] Playthrough: Zelda A Link to the Past</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLFdQ3YMJDN9jbWIFvmjgvolBLieiUkP2o'>[Entertainment\Videogames] Playthrough: Zelda Breath of the Wild</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLDgypNs3MY2RVI1ZpZX0tDE_JW3q3zgMT'>[Entertainment\Videogames] Playthrough: Zelda Breath of the Wild (No Commentary)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLEE458B6CFC6D6035'>[Entertainment\Videogames] Playthrough: Zelda Ocarina of Time</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLF41D831CF4427BE5'>[Entertainment\Videogames] Playthrough: Zelda Majora's Mask</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRNkqeo_T2DJ-pOVVlgzt_GW'>[Entertainment\Videogames] Playthrough: Zelda Phantom Hourglass </a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRM_YCtn-d-RAh__2lNr-5ny'>[Entertainment\Videogames] Playthrough: Zelda Skyward Sword </a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRPVCwyLyphrwVDdqhhSyC9n'>[Entertainment\Videogames] Playthrough: Zelda Tri-Force Heroes</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLFdQ3YMJDN9itelE3ZIIpsP0lPCuUWxtC'>[Entertainment\Videogames] Playthrough: Zelda Twilight Princess</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL_lABM4PHKBKFfw25CMPWmsQYKtT2wDzx'>[Entertainment\Videogames] Playthrough: Zelda Wind Waker</a></li>
  </ul>
</div>
<script>
window.parent.resizeIframeFavs();
</script>