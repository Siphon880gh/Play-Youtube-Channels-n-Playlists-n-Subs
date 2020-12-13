<script>var prependPath="../";</script>
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
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUQMyhrt92_8XM0KgZH6VnRg'>[Curiosity\business] Company Man</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UU_yjveGdyx6mqqHkHaD-_bg'>[Curiosity\medical] Larry Mellick (ER procedures)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL2Fa-9lE-_PwvJECVPzyhTJCFq5nqOJHi'>[Curiosity\medical] ICU Documentary</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL2Fa-9lE-UUKcWSiffY8MpZ3NKav8LeRA'>[Curiosity\nutrition] Motivational Doc</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUeZLO2VgbZHeDcongKzzfOw'>[Curiosity\music] 8-bit Music Theory</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUnkp4xDOwqqJD7sSM3xdUiQ'>[Curiosity\music] Adam Neely</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUUyeh8X37t_xJ77M6gHb1PQ'>[Curiosity\music] MangoldProject</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUyy7dZhgfeMMctSoo3wDXlQ'>[Curiosity\music] Thomas Game Docs (Occasional videogame music theory)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZiXxYJant9PXtY9AB4aaJ4'>[Curiosity\music] Music Theory</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUC552Sd-3nyi_tk2BudLUzA'>[Curiosity\science] AsapScience</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUHYoe8kQ-7Gn9ASOlmI0k6Q'>[Curiosity\science] Food Theorist</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUeTTmfuH2CbbBHWLiyrNsuQ'>[Education\medical] AllNursingNotes</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6b9rFWGl5vOwkG5m6imCkwX'>[Education\medical] CCRN Disks</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUMSsLqxqvZsNXi0Z-VjN89A'>[Education\medical] Doctor Mike Hansen</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUOKni4MYHRT45UgyMM2f0QQ'>[Education\medical] MaiMaryRN</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUUxQWmWk1_Hk9iDRKvhH29Q'>[Education\medical] Simple Nursing</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUKOvOaJv4GK-oDqx-sj7VVg'>[Education\medical] Chubbyemu</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUyjFUnWxd9ymR9Z8QXAlV3g'>[Education\medical] Dr Adeleke</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUuDh74_iAumVGF8AM4FJoUw'>[Education\medical] MainMD</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUG-iSMVtWbbwDDXgXXypARQ'>[Education\medical] MedCram</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUjMCFF9O02YXlJ1MYll3k0Q'>[Education\medical] Pharmacy</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZadXv0lxlUc-Ru89BzObZX'>[Fitness] ~Bodybuilding</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6YV7yaU-Uk7Sl_od8dC5jS0'>[Fitness] ~Design</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6bdXPppP_X4ohADNCzeMKj8'>[Fitness] ~Flexibility</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aYMCXUdkUqkzaOy73IyAhp'>[Fitness] ~Food Prep</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UURLOLGZl3-QTaJfLmAKgoAw'>[Fitness] Alan Thrall</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUe0TLA0EsQbE-MjuHXevj2A'>[Fitness] Athlean</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UU7aE5B-ZFEAoumyj6FaJ7lg'>[Fitness] Bradley Martyn</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUduKuJToxWPizJ7I2E6n1kA'>[Fitness] BroScienceLife</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUKf0UqBiCQI4Ol0To9V0pKQ'>[Fitness] Buff Dudes</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UU_7lEuEKvFt63jtvZYwlHMQ'>[Fitness] Eugene Teo</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUg_tz2iw7_-gf2xIL-DdM2g'>[Fitness] Kali Muscle</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UULqH-U2TXzj1h7lyYQZLNQQ'>[Fitness] Greg Doucette</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UU68TLK0mAEzUyHx5x5k-S1Q'>[Fitness] Jeff Nippard</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UU1XHNZDn3btv7454Pkz7THg'>[Fitness] Scooby1961</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUXrqErU_TjqiHAHJkzITAvg'>[Fitness] Shredded Sports Science</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUCCbzhtMMAxjSBmfRvd-7yw'>[Fitness] Shytown Fitness</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UU0ASolYU_Yh3yShLFQC0stg'>[Fitness] Strength Camp</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUqjwF8rxRsotnojGl4gM0Zw'>[Fitness] ThenX</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUwFplYvhHks5TV3bWxUrU9A'>[Fitness\Physical.Therapy] Back Intelligence</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUmTe0LsfEbpkDpgrxKAWbRA'>[Fitness\Physical.Therapy] Bob & Brad</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6YF7gunvezRd6wB5loe_giG'>[GF] Relationships</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='PL7267CDBCFA3172CE'>[GF] Relationships - Body Language</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUtQEjfEFzba37ozoXK40r3w'>[GF] Relationships - How to Beast</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUO1cgjhGzsSYb1rsB4bFe4Q'>[Programming\JS] Fun Fun Function</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUsLo154Krjwhoz8W00N8ItA'>[Programming\JS] Keep On Coding</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist(event, $(this).data("playlist-id"));' data-playlist-id='UUTBGXCJHORQjivtgtMsmkAQ'>[Programming\JS] Steve Griffith</a></li>
  </ul>
</div>
<script>
window.parent.resizeIframeFavs();
More = new More();
</script>