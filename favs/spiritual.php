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
  {id: 'UUnR2VoUqv9OF2oW4OmGZMzg', name: 'ASMR: Etc'},
  {id: 'PLzg85AHZsA6aXr7Ynil9HHLAxgQuTMqBP', name: 'ASMR: Strght'},
  {id: 'UUlqNSqnWeOOUVkzcJFj4rBw', name: 'ASMR: Tingting'},
  {id: 'PLzg85AHZsA6arBn4X72pJGYbPc6CIZDVS', name: 'Sxy Str MV'},
  {id: 'PLzg85AHZsA6asto33Xtovd4vvnZku-UrC', name: 'Sleep: Yawning is contagious'},
  {id: 'PLzg85AHZsA6a4Nd9lixatDBgpQ2igyzwi', name: 'Effectiveness'},
  {id: 'PLzg85AHZsA6ZxIHsGHL21rGYk4J5W8-0Q', name: 'Misc: Nofap'},
  {id: 'PLzg85AHZsA6ZywR0RV2Qb_Ioycr0Kt3Ri', name: 'NLP'},
  {id: 'PLzg85AHZsA6aIplM6VEtNHYPzwJjpHxFZ', name: 'NLP do music'},
  {id: 'PLzg85AHZsA6bqXyObhmpKsHwvjx7fI6Ot', name: 'NLP Gym'},
  {id: 'PLzg85AHZsA6YX5IsEVSBfIcn77PwoNGAj', name: 'NLP release trauma'},
  {id: 'PLzg85AHZsA6bq3LXCQWIiZR_4D9uAokja', name: 'Release: Trauma Approach (Catharsis)'},
  {id: 'PLzg85AHZsA6Yyx-FqpmL05KhDpN2ZjULY', name: 'Healing: Christian'},
  {id: 'PLzg85AHZsA6Y-rZ-owmqJ6mSiAQu81Yi_', name: 'LOA'},
  {id: 'PLzg85AHZsA6Z_IcNRi-K4FZyBbmKQVb-q', name: 'Theory: Misc'},
  {id: 'PLzg85AHZsA6ap8McAmBX-Nj8OnMezvMaM', name: 'Astral Parasites: Misc'},
  {id: 'PLzg85AHZsA6ZPa4zA9IBJ-yAqbbeJFR9A', name: 'Astral Parasites: Christian Mixed'},
  {id: 'PLzg85AHZsA6a3J2CyRJMdlrnzJxDq6CKx', name: 'Astral Parasites: Christian'},
  {id: 'PLzg85AHZsA6aukiwowtOOwPSDJ2jWdBye', name: 'Env: Misc Clearing'},
  {id: 'PL0PC_MNnxRcS_6mt1KoMlbVkDV8mW6YUa', name: 'Env: Freq Clearing'},
  {id: 'PLzg85AHZsA6ZFpPlpIdo5Y9F59Qxru_3O', name: 'Warfare Misc: Teaching'},
  {id: 'PLzg85AHZsA6ZyTP5_EdIok8ZuE3yCxbnq', name: 'Warfare Misc: Techniques'},
  {id: 'UUn8OYopT9e8tng-CGEWzfmw', name: '[Paranormal/Mysteries] Atrocity Guide'},
  {id: 'UUwufEauqjSkYmHg-pGuw3tw', name: '[Paranormal/Mysteries] Cryptid Hunter'},
  {id: 'UUpFFItkfZz1qz5PpHpqzYBw', name: '[Paranormal/Mysteries] Nexpo'},
  {id: 'UUiEK-SwMjiYsiWzXvOKqKdg', name: '[Paranormal] AmysCrypt'},
  {id: 'UUAdu0daWv0wjvojTVZvQ37Q', name: '[Paranormal] Disclose Screen'},
  {id: 'UUsggmbrFzu-ibXcSbMl4e6Q', name: '[Paranormal] Franko TV'},
  {id: 'UUbAoEIuTWHuYCqv-Py6ZzQg', name: '[Paranormal] Glowing Ghost Paranormal (Michael Scot)'},
  {id: 'UUtPrkXdtCM5DACLufB9jbsA', name: '[Paranormal] Mr Ballen'},
  {id: 'UUnM02drQP-dF7WMgtJHR4Xw', name: '[Paranormal] Mr Nightmare'},
  {id: 'UU7xm1xzLyrcJgWUX2cqKGQw', name: '[Paranormal] ParaBreakdown'},
  {id: 'UUGgdhMFHiKym-z4G7IB4BYw', name: '[Paranormal] Sir spooks'},
  {id: 'UUYaPVYvW8-re6XQA00EGxkg', name: '[Paranormal] The Haunted Side'},
  {id: 'UUsSRxYAK0PiA7d0XUR6sPFA', name: '[Paranormal] The Paranormal Files'},
  {id: 'UUfj9TK1mhbIWpcmP5rcIfYQ', name: '[Paranormal] The Paranormal Scholar'},
  {id: 'UUja26BEv6YUHC2QLShBdt8w', name: '[Paranormal] Huff Paranormal'}
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