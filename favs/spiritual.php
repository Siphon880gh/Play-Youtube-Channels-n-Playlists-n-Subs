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
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUnR2VoUqv9OF2oW4OmGZMzg'>ASMR: Etc</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aXr7Ynil9HHLAxgQuTMqBP'>ASMR: Strght</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUlqNSqnWeOOUVkzcJFj4rBw'>ASMR: Tingting</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6arBn4X72pJGYbPc6CIZDVS'>Sxy Str MV</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6asto33Xtovd4vvnZku-UrC'>Sleep: Yawning is contagious</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6a4Nd9lixatDBgpQ2igyzwi'>Effectiveness</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZxIHsGHL21rGYk4J5W8-0Q'>Misc: Nofap</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZywR0RV2Qb_Ioycr0Kt3Ri'>NLP</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aIplM6VEtNHYPzwJjpHxFZ'>NLP do music</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6YX5IsEVSBfIcn77PwoNGAj'>NLP release trauma</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6bq3LXCQWIiZR_4D9uAokja'>Release: Trauma Approach (Catharsis)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6Yyx-FqpmL05KhDpN2ZjULY'>Healing: Christian</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6Y-rZ-owmqJ6mSiAQu81Yi_'>LOA</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6Z_IcNRi-K4FZyBbmKQVb-q'>Theory: Misc</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ap8McAmBX-Nj8OnMezvMaM'>Astral Parasites: Misc</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZPa4zA9IBJ-yAqbbeJFR9A'>Astral Parasites: Christian Mixed</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6a3J2CyRJMdlrnzJxDq6CKx'>Astral Parasites: Christian</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6aukiwowtOOwPSDJ2jWdBye'>Env: Misc Clearing</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PL0PC_MNnxRcS_6mt1KoMlbVkDV8mW6YUa'>Env: Freq Clearing</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZFpPlpIdo5Y9F59Qxru_3O'>Warfare Misc: Teaching</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='PLzg85AHZsA6ZyTP5_EdIok8ZuE3yCxbnq'>Warfare Misc: Techniques</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUn8OYopT9e8tng-CGEWzfmw'>[Paranormal/Mysteries] Atrocity Guide</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUwufEauqjSkYmHg-pGuw3tw'>[Paranormal/Mysteries] Cryptid Hunter</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUpFFItkfZz1qz5PpHpqzYBw'>[Paranormal/Mysteries] Nexpo</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUiEK-SwMjiYsiWzXvOKqKdg'>[Paranormal] AmysCrypt</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUAdu0daWv0wjvojTVZvQ37Q'>[Paranormal] Disclose Screen</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUsggmbrFzu-ibXcSbMl4e6Q'>[Paranormal] Franko TV</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUbAoEIuTWHuYCqv-Py6ZzQg'>[Paranormal] Glowing Ghost Paranormal (Michael Scot)</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUtPrkXdtCM5DACLufB9jbsA'>[Paranormal] Mr Ballen</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUnM02drQP-dF7WMgtJHR4Xw'>[Paranormal] Mr Nightmare</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UU7xm1xzLyrcJgWUX2cqKGQw'>[Paranormal] ParaBreakdown</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUGgdhMFHiKym-z4G7IB4BYw'>[Paranormal] Sir spooks</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUYaPVYvW8-re6XQA00EGxkg'>[Paranormal] The Haunted Side</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUsSRxYAK0PiA7d0XUR6sPFA'>[Paranormal] The Paranormal Files</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUfj9TK1mhbIWpcmP5rcIfYQ'>[Paranormal] The Paranormal Scholar</a></li>
    <li><a href="javascript:void(0);" onclick='window.parent.urlChange.playlist($(this).data("playlist-id"));' data-playlist-id='UUja26BEv6YUHC2QLShBdt8w'>[Paranormal] Huff Paranormal</a></li>
  </ul>
</div>
<script>
window.parent.resizeIframeFavs();
More = new More();
</script>