<script>var prependPath="./";</script>
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
     * that opens more channel collections, including those
     * that may be more personal. The combo here is
     * click x2, then click-hold for 2 seconds
     * 
    */
    ?>
    <a href="javascript:void(0);" onclick="More.count();" onmousedown="More.start();" onmouseup="More.stop();">Watch videos from Youtube channels:</a>
    <div class="float-right float-right-buttons" style="padding-right: 5px;">
      <i id="random" class="fa fa-random clickable" onclick="RandomPlaylist.select();" style="margin-left:3px;"></i>
      <div style="width:1px; height:10px;"></div>
      <i id="manual" class="fa fa-cloud-upload-alt clickable" onclick="ManualPlaylist.prompt();"></i>
    </div> <!-- /float-right -->
  </div> <!-- /header -->
  <ul>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU9avFXTdbSo5ATvzTRnAVFg'>[Asian Community] Fung Bros</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUK7IIV6Q2junGSdYK3BmZMg'>[Celebs] TMZ</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU8OJagbECdNfwOpAxHPxSgg'>[Comedy] Cassady Campbell</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUcoDpbA4c9HQY0AjKHSlKLg'>[Comedy] Cer Spence</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUPHeAtHSXnymNh8b_Kkj2rw'>[Comedy] Cherdleys</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU7zsxKqd5MicTf4VhS9Y74g'>[Comedy] Kurtis Conner</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUR0VLWitB2xM4q7tjkoJUPw'>[Comedy] Creations Ross</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUXHz8Ai5458HM9XK3n-UhTQ'>[Comedy] Eric Kanevsky</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUkhxWF5CTMUgxneqAFP96LQ'>[Comedy] Nelk</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUdcUmdOxMrhRjKMw-BX19AA'>[Comedy] Niko Omilana</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUxOGHW-aqciBe5Wjq8ltzOg'>[Comedy] Ownage Pranks</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUWGmhDFi3fB5DA1nuVRsPIQ'>[Comedy] Reactistan</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUhQvODKsbmt-A8bgLauCMqA'>[Comedy] Steezy Kane</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUvkdpLN1agXoiRWEWTtXqjw'>[Comedy] TopNotch Idiots</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUp5x5kxCgg_A220jNIsLyfA'>[Conspiracy.Theories\Christian] A Call for an Uprising (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUo8fiE2-s0SZu6Onb8lNLMQ'>[Conspiracy.Theories\Christian] Scariest Movie Ever (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUGe_v1bnmWI0QBoK1ArnLmg'>[Conspiracy.Theories\Christian] Spencer Smith (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aMazn2DAMV-mscaVPONISA'>[Concentration] ~Coding music</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda'>[Concentration] ~Busy Sounds</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZTv128rwSO691EMOVsOih1'>[Concentration] ~Time-Lapsed Music</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUeLc4heXqG9dtL7jiPHMBfQ'>[Curiosity\news\China] The Epoch Times (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUgFP46yVT-GG4o1TgXn-04Q'>[Curiosity\news\China] China Uncensored (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU8Ioh4atNDO5a0Vy2qFmauQ'>[Curiosity\news\China] NTD (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUBOqkAGTtzZVmKvY4SwdZ2g'>[Curiosity\news\China] NTD - China in Focus (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUG8yy4W8nBbTle9YSpCqXeg'>[Curiosity\news\China] Crossroads with Joshua Philipp</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUZz8q2tZC7QuQiFwPKQ4Bug'>[Curiosity\news\China] TFIglobal</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU_gUM8rL-Lrg6O3adPW9K1g'>[Curiosity\news\China] Wion</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUknLrEdhRCp1aegoMqRaCZg'>[Curiosity\news\global] DW News</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU_7vFlErTHxVD-IFNB-BFCg'>[Curiosity\news\local] America Uncovered</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUHS7hIC7dYmul82KJ9p57Dw'>[Curiosity\reddit] Panda Reddit</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUTUJrMRaPvlaqkQ6YpVM08w'>[Curiosity\reddit] Radio TTS</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUBkiU4KW2amn5lI480LASgQ'>[Curiosity\reddit] Reddit Aliens</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU2sabBFKKPFcz8tg0lZwzvg'>[Curiosity\reddit] Reddit on Tap</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUgSmBJD9imASmJRleycTCwQ'>[Curiosity\reddit] Sir Reddit</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUYUunpzH7_WtYwvss2Ja9GQ'>[Curiosity\reddit] Storytime with Reddit</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUegtkvhLG9XYzWfRR99ateQ'>[Curiosity\reddit] Updoot</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUV3Nm3T-XAgVhKH9jT0ViRg'>[Curiosity\topics] AJ+</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUyy7dZhgfeMMctSoo3wDXlQ'>[Curiosity\videogames\music] Thomas Game Docs</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUKaOcwliIgmzP87XMT0Arpw'>[Entertainment\Anime\DBZ] ExperGamez</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUgIqhB8oD5SZ0yUSp71VCOQ'>[Entertainment\Anime\DBZ] Geekdom101</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUObfIFcPxDOsyNXTG3ISyBg'>[Entertainment\Anime\DBZ] MaSTAR Media</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUFNZ1cFq0TI6oL1UjFOv2Xw'>[Entertainment\Anime\DBZ] UnrealEntGaming</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUbDM52R_HA2UweV4IcwOT_w'>[Entertainment\Videogames] GenerationGapGaming</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLs1-UdHIwbo497viX2cCYHUNVBsORkhbl'>[Entertainment\Videogames] Playthrough: Call of Duty WW2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXROBlFPHkRJ2-AgOq8ndsPJK'>[Entertainment\Videogames] Playthrough: Chrono Trigger</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLD14CA711E34ACAFD'>[Entertainment\Videogames] Playthrough: EarthBound</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLR2vpfke_UnB6o0t0bpiZo3wuUg0ql1pK'>[Entertainment\Videogames] Playthrough: Half Life 1</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLj_Goi54wf0d2xk_aLt-cFer_XTqI4qaN'>[Entertainment\Videogames] Playthrough: Half Life 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLMBYlcH3smRzz8VqMEUO2i9Vu3ynWAkQ0'>[Entertainment\Videogames] Playthrough: God of War</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLAnfG6CHIYjwTwqSJgWMFktc6Xo1VxJ8B'>[Entertainment\Videogames] Playthrough: Halo 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLAnfG6CHIYjx8Da2Aw1q0hbhuKE3pbQYz'>[Entertainment\Videogames] Playthrough: Halo Combact Evolved</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRMFBAf-HK4XtmjWDNSKoPVI'>[Entertainment\Videogames] Playthrough: Kid Icarus Uprising</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL8EC21DD302AEF995'>[Entertainment\Videogames] Playthrough: Paper Mario</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUQxM_1P5fBuIzo51njAG1pV'>[Entertainment\Videogames] Playthrough: Paper Mario Color Splash</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUTtbcVGfBfZi7Z4UvY7BZSv'>[Entertainment\Videogames] Playthrough: Paper Mario Sticker Star</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLCD434C085CB7C8EA'>[Entertainment\Videogames] Playthrough: Paper Mario, Super</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLF00C64B86BD75D79'>[Entertainment\Videogames] Playthrough: Paper Mario Thousand-Year Door</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUYmGCAwhHmpV4LZrbvpjaTg'>[Entertainment\Videogames] Playthrough: Resident Evil 3 Remake ~Jill Mods</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUQWf9JidznuOIvMWIBHmF2v'>[Entertainment\Videogames] Playthrough: Super Mario 64</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUTBMe6blf5YMaU2DhdtF4Iv'>[Entertainment\Videogames] Playthrough: Super Mario Galaxy</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXROScbAOakRhAsINVoa9sSmh'>[Entertainment\Videogames] Playthrough: Super Mario Galaxy 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL57hJfweW_2s1aLE9WtLCzKAAWWK0VJeH'>[Entertainment\Videogames] Playthrough: Super Mario RPG</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLkR3RWLcKJUSMTYZ3NAXVCk4S3PflWaVf'>[Entertainment\Videogames] Playthrough: Super Mario Sunshine</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRM5w3LkVEVGE_7dbvf4n41S'>[Entertainment\Videogames] Playthrough: Super Mario Superstar Saga</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL-7t9DoIELCQPdyHZAy5xdfDXM6HDpEkv'>[Entertainment\Videogames] Playthrough: Super Mario Odyssey</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aglhKWjYkDFci7FwrBpYJP'>[Entertainment\Videogames] Playthrough: Torchlight 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRM2_xfO1O6P040gwIwlhqcP'>[Entertainment\Videogames] Playthrough: Xenoblade Chronicles</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRPQf_RR-km-i34wPrQrhncN'>[Entertainment\Videogames] Playthrough: Xenoblade Chronicles 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6YbtfGkRpvwToPqZe9ZzQF_'>[Entertainment\Videogames] Playthrough: Zelda ~Romhacks</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRMHL_fIWlbHhyxh8d2GhM_r'>[Entertainment\Videogames] Playthrough: Zelda A Link to the Past</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLFdQ3YMJDN9jbWIFvmjgvolBLieiUkP2o'>[Entertainment\Videogames] Playthrough: Zelda Breath of the Wild</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLEE458B6CFC6D6035'>[Entertainment\Videogames] Playthrough: Zelda Ocarina of Time</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLF41D831CF4427BE5'>[Entertainment\Videogames] Playthrough: Zelda Majora's Mask</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRNkqeo_T2DJ-pOVVlgzt_GW'>[Entertainment\Videogames] Playthrough: Zelda Phantom Hourglass </a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRM_YCtn-d-RAh__2lNr-5ny'>[Entertainment\Videogames] Playthrough: Zelda Skyward Sword </a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_sYhAj0WXRPVCwyLyphrwVDdqhhSyC9n'>[Entertainment\Videogames] Playthrough: Zelda Tri-Force Heroes</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLFdQ3YMJDN9itelE3ZIIpsP0lPCuUWxtC'>[Entertainment\Videogames] Playthrough: Zelda Twilight Princess</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL_lABM4PHKBKFfw25CMPWmsQYKtT2wDzx'>[Entertainment\Videogames] Playthrough: Zelda Wind Waker</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU3gWv-0A3qEeFBJESlsJa0g'>[Motivation] HES Motivation</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUrTlvJkxo5LmCvwz8k3aXLA'>[Motivation\Workout] Beast Motivation</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUNMJLu39B4DPjSM_USGYTUw'>[Motivation\Workout] Alpha Motivation</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6Z3jwM_jYHoi40AMhPUeU7_'>[Music] ~Intense</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZiXxYJant9PXtY9AB4aaJ4'>[Music] ~Music Theory</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUDsYDtM5BNXz-6OzZlad4ew'>[Music] Video Mashup - Lana Del Rey 1</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUpqUuir75c-fuyvpMvyxbqQ'>[Music] Video Mashup - Lana Del Rey 2</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU5-gWZXAQqSGVfPHkA7NRiQ'>[Music] Bebe Rexha</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUiGm_E4ZwYSHV3bcW1pnSeQ'>[Music] Billie Eilish</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLUBfAAO0X0TfEgkI6c-nS8vcJ5UDiFnKI'>[Music] Halsey</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU-J-KZfRV8c13fOCkhXdLiQ'>[Music] Dua Lipa</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUi8e0iOVk1fEOogdfu4YgfA'>[Movies] Movieclips Trailers</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUT0dmfFCLWuVKPWZ6wcdKyg'>[Opinion\Doc] James Jani</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU3WGuV8xgGZAk2B2itJqgDg'>[Opinions\Films] Let Me Explain</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUfwE_ODI1YTbdjkzuSi1Nag'>[Opinions\Entertainment] TheQuartering</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUJfJWct8jN1RpCuVWk3zHTA'>[Opinion\Memes] Daryl Talks Games</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU3jdnIP2u5hCJpVZ-TuDrCg'>[Opinion\Memes] Whang!</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUfAPTv1LgeEWevG8X_6PUOQ'>[Opinions\Videogames] GameXplain</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUcZ959PMwCmSG1mP1gMmDBw'>[Opinions\Videogames] Mish Koz</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUA5RGaQc-a8tIX_AqTTmWdw'>[Opinions\Videogames] RGT 85</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU5UYMeKfZbFYnLHzoTJB1xA'>[Opinions\Videogames] Schaffrillas Productions</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU0VVYtw21rg2cokUystu2Dw'>[Opinions\Videogames] SmallAnt</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUoIXnB865l9Ex9zs4OIXTdQ'>[Opinions\Videogames] Spawn Wave</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUQAi3Q1KiKlOERMT6Uvnsvw'>[Opinions\Videogames] Zelda Croton</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUudx6plmpbs5WtWvsvu-EdQ'>[Opinions\Videogames] Zelda Zeltik</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUhc-m3saf8K2oJHDSsnsj_A'>[Opinion] Elvis The Alien</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUr0XW6TU9XVWlWPpEwEyf3g'>[Opinion] Jamari</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUoLUji8TYrgDy74_iiazvYA'>[Opinion] Jarvis Johnson</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUyhOl6uRlxryALlT5yifldw'>[Opinion] J.J. McCullough</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUhhyfYkhuVrzmvDr-WqpDSw'>[Opinion] Not Even Emily</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUUWhqBJfvkZogLBxFttN4_g'>[Opinion] Omni</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUittVh8imKanO_5KohzDbpg'>[Opinion] Paul Joseph Watson (Propaganda)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUq6VFHwMzcMXbuKyG7SQYIg'>[Opinion] penguinz0</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUlFSU9_bUb4Rc6OYfTt5SPw'>[Opinion] Philip DeFranco</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUsvUzBAxu2t5e_qOEr6iczQ'>[Opinion] Psych IRL</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUtMVHI3AJD4Qk4hcbZnI9ZQ'>[Opinion] SomeOrdinaryGamers</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUzQUP1qoWDoEbmsQxvdjxgQ'>[Podcast] Joe Rogan</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUnxGkOGNMqQEUMvroOWps6Q'>[Podcast] Joe Rogan (JRE Clips)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUeR0n8d3ShTn_yrMhpwyE1Q'>[Reviews\Food] TheReportOfTheWeek aka ReviewBrah</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUBNG0osIBAprVcZZ3ic84vw'>[Scams] Jim Browning</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6Z8EmcIryHG8yM7_qvkFCXE'>[Sleep] Boring</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6asto33Xtovd4vvnZku-UrC'>[Sleep] Yawning</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aPadUZd7IuCaCtXEDPqQLd'>[Sleep] White Noise</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZR2sayII1ahKM5jiwQHTAY'>[Sleep] Tips</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUwNPa8fSXzzAZuT9859GVhg'>[Travel\China] AVD China</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUxDZs_ltFFvn0FDHT6kmoXA'>[Travel\China] Bald and bankrupt</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUhvithwOECK5g_19TjldMKw'>[Travel\China] Laowhy86</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUGXQvAsqUJrthTvTorUAGAw'>[Travel\California] Vince Nguyen</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUJb5MIGYcoXHKqMtom0kfhw'>[Travel\local] Bert Isobe</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU-EwLNFl_BETcI1naM_6FcQ'>[Travel\local] CA Travel Tips</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUWdcosWNz26bf9Jri8DTnow'>[Travel\local] Christophe Choo</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUw8_bi1G4duPevdfktipGOA'>[Travel\local] Yellow Productions</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUKr68ZJ4vv6VloNdnS2hjhA'>[Travel\global] Harald Baldr</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUpQu57KgT7gOoLCAu3FFQsA'>[Travel\global] Voice of North Korea</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUyEd6QBSgat5kkC6svyjudA'>[Travel\food] Mark Wiens</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU3Vuq4Q1bKFtAiKYlwRv3oA'>[Travel] Peter Santenello</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUl7mAGnY4jh4Ps8rhhh8XZg'>[Travel] Serpentza</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU12jo_pTYrNmDAscoqfYIBg'>[Travel] Tall Travels</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUO5B9ZW7miyP97QN8NtU48w'>[Travel] Way Beyond Pad Thai</a></li>
  </ul>
</div>
<script>
window.parent.resizeIframeFavs();
More = new More();
</script>