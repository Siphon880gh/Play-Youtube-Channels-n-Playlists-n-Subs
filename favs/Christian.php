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
  {id: 'UU7dZK1kpErY_9RHgLxzIoXg', name: 'Sins deliverance - From Kick Devils Out'},
  {id: 'UU4HG-iZ6MwZsuL6k2FWXUdw', name: 'Sins deliverance - From MrPastor77'},
  {id: 'PLzg85AHZsA6Yjk1EpEF-C_W2QsIuwTkMh', name: 'Sins deliverance (Latin)'},
  {id: 'PLzg85AHZsA6bX9jxGv3_1dg5opFKBjD6V', name: 'Sins deliverance and prayers (energetic)'},
  {id: 'PLzg85AHZsA6a__6-jtxoEmqStfDETAj21', name: 'Sins deliverance and teachings'},
  {id: 'PLzg85AHZsA6YKQ68JXiT2H-ljmv2xTuzg', name: 'Sins deliverance and teachings / Healing'},
  {id: 'PLzg85AHZsA6aTkEYnE3ZybEjhzPSXwRHl', name: 'Sins deliverance and teachings / Occult'},
  {id: 'PLzg85AHZsA6bgTbES3-8jkf31vySJRQ6u', name: 'Sins deliverance'},
  {id: 'PLzg85AHZsA6bVPnlS-w4qC5CUVpbDRB3S', name: 'Sins deliverance / Productive life'},
  {id: 'PLzg85AHZsA6ZKToiZsS81s6jPIjY7sQK-', name: 'Sins deliverance / Sleep / Healing'},
  {id: 'PLzg85AHZsA6adJZdHTlxi7cXcynmsXKbm', name: 'Sins deliverance / Sleep / Insomnia'},
  {id: 'PLzg85AHZsA6ZoNw5NOFBreKChtAVD93F7', name: 'Sins deliverance / Sleep / Night Terrors'},
  {id: 'PLzg85AHZsA6Z0FWHWr9huRGFyHQ-H8tH_', name: 'Sins deliverance / Sx'},
  {id: 'PLzg85AHZsA6bCGOv3UPzJ3EgdWmkqlkwj', name: 'Sins deliverance'},
  {id: 'PLzg85AHZsA6YB4RNJ26tzLyZwaIy1PyGK', name: 'Sins deliverance favs'},
  {id: 'PLzg85AHZsA6bCGOv3UPzJ3EgdWmkqlkwj', name: 'Sins prayers'},
  {id: 'PLzg85AHZsA6aF7858-IVFsM9MpYiC3sHC', name: 'Sins prayers / Sleep / Insomnia'},
  {id: 'PLzg85AHZsA6Y5tIujyeA3i6QT5iq4Zv-U', name: 'Sins prayers / Warfare'},
  {id: 'PLzg85AHZsA6YoNIlCtd3CzOWy_iZpoOb8', name: 'Sins teachings'},
  {id: 'UUxsG-yjeM5dH_YOHpKqXDmg', name: 'Sins teachings (Doreen Virtue)'},
  {id: 'PLzg85AHZsA6Y8WLVbM7zI0KlES0srqXpu', name: 'Sins teachings - Consistent'},
  {id: 'UUBR4WLqe5fivikfxn9Tcn1A', name: 'Sins teachings - Consistent with deliverance (Chris LaSala)'},
  {id: 'PLzg85AHZsA6bU8NALCZEMooUeRQZ6fbeo', name: 'Sins teaching - Insomnia'},
  {id: 'PLzg85AHZsA6Y9ld1st3TTi4KVg6bESniZ', name: 'Sins testimonies'},
  {id: 'UUUFfRr75RtMmT3ArGNZeZgg', name: '[Sins testimonies] Precious Testimonies'},
  {id: 'PLzg85AHZsA6aeGX5E4gisCapdbxrx1fIW', name: 'Sins verses sleep'},
  {id: 'UUv9-3aFss806GZFxNoTVduQ', name: '[General teaching/Consistent] Marcus Rogers'},
  {id: 'UUMfVJ7yCHEvS9xnmu2-Fl_w', name: '[General teaching/Consistent] Revelations of Jesus Christ'},
  {id: 'UU0f2dw3mHGg-LFjr61q4Q2g', name: '[General teaching/Consistent] The Demonic Realm Exposed'}
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