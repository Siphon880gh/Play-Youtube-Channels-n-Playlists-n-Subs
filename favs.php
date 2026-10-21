<script>window.parent.prependPath="./";</script>
<?php $prependPath="./"; ?>
<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href="<?php echo $prependPath; ?>assets/css/favs.css?v=8" rel="stylesheet"></link>
<link href="<?php echo $prependPath; ?>assets/css/collection-themes.css?v=4" rel="stylesheet"></link>
<script src="<?php echo $prependPath; ?>assets/js/favs.js"></script>
<script src="<?php echo $prependPath; ?>env/playlist-icons.js?v=7"></script>
<?php include __DIR__ . "/env/icon-fonts.php"; ?>
<div id="favs">
  <?php include($prependPath."assets/templates/favs.php"); ?>

  <div class="favs-controls">
    <div class="quick-filters">

      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Mood OR NLP") { $("#playlist-filter").val("Mood OR NLP") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Mood OR NLP]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="NLP") { $("#playlist-filter").val("NLP") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[NLP]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Travel") { $("#playlist-filter").val("Travel") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Travel]</a>
      <span>|</span>
      <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="Comedy") { $("#playlist-filter").val("Comedy") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");'>[Comedy/Wake]</a>

      <div style="width:1px; height:5px;"></div>
      <a href="javascript:void(0)" onclick='RandomPlaylist.select();'>[[Rand]]</a>
      <span>|</span>
      <a target="_blank" href="https://docs.google.com/spreadsheets/d/12kDsyiiyeEgl7t87sRy1TkOd4XH4cRa-/edit?gid=409693151#gid=409693151">[[Motiv]]</a>

      <div style="width:1px; height:5px;"></div>
      <a target="_blank" href="//www.youtube.com/watch?v=q1YVvndNyqM">[EMDR]</a>
      <span>|</span>
      <a target="_blank" href="//www.youtube.com/watch?v=nMfPqeZjc2c">[White Noise Background]</a>

    </div>

    <div class="emoji-filters">
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="👀") { $("#playlist-filter").val("👀") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");' style="text-decoration:none;">👀</a>
    <span>&nbsp;</span>
    <a href="javascript:void(0)" onclick='if($("#playlist-filter").val()!=="👁️") { $("#playlist-filter").val("👁️") } else { $("#playlist-filter").val("") }  $("#playlist-filter").trigger("input");' style="text-decoration:none;">👁️</a>
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
    </div>

    <div class="filter-bar">
    <a class="filter-reset" href="javascript:void(0)" onclick='$("#playlist-filter").val("").trigger("input");'><i class="fa fa-eraser" style="color:rgba(122,0,0);"></i></a>
    <span>&nbsp;</span>
    <label for="playlist-filter" onclick='$("#playlist-filter").val(""); filterListItems("", $(".playlists-target")); saveFilteredForRefresh("");'>Filter:</label>
    <input id="playlist-filter" class="playlist-filter" type="text" oninput="filterListItems($(event.target).val(), $('.playlists-target')); saveFilteredForRefresh($(event.target).val());" autocomplete="on" placeholder="term OR term OR...">
    </div>
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
  let $sectionHeaders = listItems.find("li.section-header");
  let $groupHeaders = listItems.find("li.section-group");

  if(userFilterText.length) {
    $allListItems.hide();

    var $filteredIn = null;
    var terms = userFilterText.split("OR").map(term => term.trim())
    terms.forEach(term=>{
      if(term.length===0) return true;

      var $matches = listItems.find(`li:not(.section-header):not(.section-group):contains('${term}')`);
      if($filteredIn===null) {
        $filteredIn = $matches;
      } else {
        $.merge($filteredIn, $matches);
      }
    })
    if($filteredIn!==null) {
      $filteredIn.show();
    }

    $sectionHeaders.each(function() {
      var $header = $(this);
      var $nextItems = $header.nextUntil('.section-header');
      var hasVisibleItems = $nextItems.filter(':visible').length > 0;
      if(hasVisibleItems) {
        $header.show();
      }
    });

    $groupHeaders.each(function() {
      var $header = $(this);
      var $nextItems = $header.nextUntil('.section-group');
      if($nextItems.filter(':visible').length > 0) {
        $header.show();
      }
    });
  } else {
    $allListItems.show();
  }

  syncCategoryBrowser();
} // filterListItems

function saveFilteredForRefresh(userFilterText) {
  localStorage.setItem("YT__last-filtered", userFilterText);
} // saveFilteredForRefresh

function stripHtmlFromLabel(labelText) {
  return $("<div>").html(labelText).text();
}

function extractCategoriesFromPlaylistLabel(labelText) {
  var cleanText = stripHtmlFromLabel(labelText);
  var matches = String(cleanText || "").match(/\[([^\]]+)\]/g) || [];
  return matches
    .map(function(match) {
      return match.slice(1, -1).trim();
    })
    .filter(function(category) {
      return category.length > 0;
    });
}

function getVisiblePlaylistCategories() {
  var categories = [];
  var seenCategories = {};

  $(".playlists-target li:visible").each(function() {
    var $playlistLink = $(this).find("a[data-playlist-id]").first();
    if(!$playlistLink.length) {
      return true;
    }

    extractCategoriesFromPlaylistLabel($playlistLink.text()).forEach(function(category) {
      if(seenCategories[category]) {
        return;
      }

      seenCategories[category] = true;
      categories.push(category);
    });
  });

  return categories;
}

function syncCategoryBrowser() {
  var categories = getVisiblePlaylistCategories();
  if(window.parent && typeof window.parent.renderCategoryBrowser === "function") {
    window.parent.renderCategoryBrowser(categories);
  }
}

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

function isGuideHttpUrl(value) {
  try {
    var url = new URL(value, window.location.href);
    return url.protocol === "https:" || url.protocol === "http:";
  } catch (err) {
    return false;
  }
}

function openPlaylistGuide(event) {
  event.preventDefault();
  var $el = $(event.target).closest("a");
  var url = $el.attr("data-open");
  var instructions = $el.data("instructions") || "";
  if(isGuideHttpUrl(url)) {
    var tab = window.open(url, "_blank");
    if(tab) tab.opener = null;
  }
  if(window.parent && typeof window.parent.showPlaylistGuide === "function") {
    window.parent.showPlaylistGuide(instructions);
  }
}

// Fetch and render list of playlists

function fetchPlaylist(calledCollection) {
    if(calledCollection)
      currentCollection = calledCollection;

    // debugger;

    // Private collections are never fetched as static files; they go through
    // private-favs.php, which only answers after the password was entered.
    if(isPrivateCollection(currentCollection)) {
      fetchPrivateCollection(currentCollection);
      return;
    }

    $.ajax({
        url: "./favs/" + currentCollection + ".json",
        method:"GET",
        cache: false,
        success: function(data, status) {
          if(data) {
            window.collection = data;
            applyCollectionTheme(getCollectionTheme(window.collection));
            renderPlaylists(window.collection);
          }
        }
    });
  } // fetchPlaylist

  // Collection themes: a row like { "theme": "matrix-green" } picks a preset from
  // assets/css/collection-themes.css. No row (or an unknown name) = youtube-red.
  var COLLECTION_THEMES = ["youtube-red", "matrix-green", "monochrome"];

  function getCollectionTheme(collection) {
    if(!Array.isArray(collection)) return "youtube-red";
    for(var i = 0; i < collection.length; i++) {
      var row = collection[i];
      if(row && typeof row.theme === "string") {
        var theme = row.theme.trim();
        return COLLECTION_THEMES.indexOf(theme) !== -1 ? theme : "youtube-red";
      }
    }
    return "youtube-red";
  }

  // Applies the theme to this iframe and to the parent page around it.
  function applyCollectionTheme(theme) {
    document.documentElement.setAttribute("data-yt-theme", theme);
    try {
      if(window.parent && window.parent !== window) {
        window.parent.document.documentElement.setAttribute("data-yt-theme", theme);
      }
    } catch(err) {}
  }

  function isPrivateCollection(name) {
    return typeof name === "string" && name.indexOf("Private") === 0;
  }

  function fetchPrivateCollection(name) {
    $.ajax({
        url: "./private-favs.php",
        method: "GET",
        data: { action: "collection", favs: name },
        dataType: "json",
        cache: false,
        success: function(data) {
          if(data) {
            window.collection = data;
            applyCollectionTheme(getCollectionTheme(window.collection));
            renderPlaylists(window.collection);
          }
        },
        error: function(xhr) {
          if(xhr.status === 401) {
            renderPrivateUnlockForm(name, "");
          } else if(xhr.status === 404) {
            renderPrivateMessage("That private collection was not found.");
          } else {
            renderPrivateMessage("Could not load this private collection.");
          }
        }
    });
  }

  function renderPrivateMessage(message) {
    applyCollectionTheme("monochrome");
    var $target = $(".playlists-target");
    $target.html("");
    $target.append($('<li class="not-link private-unlock-message"></li>').text(message));
    syncCategoryBrowser();
    resizeIframeNow();
  }

  // The password is only sent to the server; nothing private is in this page until it says yes.
  function buildPrivateUnlockForm(titleText, message, onUnlocked) {
    var $form = $('<form class="private-unlock-form" autocomplete="on"></form>');
    $form.append($('<p class="private-unlock-title"></p>').append('<i class="fa fa-lock" aria-hidden="true"></i> ').append(document.createTextNode(titleText)));
    var $user = $('<input type="text" name="username" autocomplete="username" value="private-playlists" hidden>');
    var $input = $('<input type="password" name="password" class="private-unlock-input" autocomplete="current-password" placeholder="Password" required>');
    var $button = $('<button type="submit" class="private-unlock-btn">Unlock</button>');
    var $msg = $('<p class="private-unlock-error" role="alert"></p>').text(message || "");
    $form.append($user, $input, $button, $msg);
    $form.on("submit", function(event) {
      event.preventDefault();
      $button.prop("disabled", true);
      $msg.text("");
      $.ajax({
        url: "./private-favs.php?action=login",
        method: "POST",
        data: { password: $input.val() },
        dataType: "json",
        cache: false,
        success: function() {
          $input.val("");
          onUnlocked();
        },
        error: function(xhr) {
          $button.prop("disabled", false);
          $input.val("").trigger("focus");
          var body = xhr.responseJSON || {};
          if(xhr.status === 429) {
            var mins = Math.max(1, Math.ceil((body.retryAfter || 60) / 60));
            $msg.text("Too many wrong passwords. Try again in about " + mins + " min.");
          } else if(xhr.status === 503) {
            $msg.text("Private collections are not set up on the server yet.");
          } else {
            $msg.text("Wrong password.");
          }
        }
      });
    });
    return $form;
  }

  function renderPrivateUnlockForm(name, message) {
    applyCollectionTheme("monochrome");
    var $target = $(".playlists-target");
    $target.html("");
    var $li = $('<li class="not-link private-unlock"></li>');
    var $form = buildPrivateUnlockForm('"' + name + '" is private.', message, function() {
      fetchPrivateCollection(name);
    });
    $li.append($form);
    $target.append($li);
    syncCategoryBrowser(); // hides the tag panel: nothing to list while locked
    resizeIframeNow();
    $form.find(".private-unlock-input").trigger("focus");
  }

  // Clicked a blurred (locked) collection in the collections panel. After unlocking,
  // the panel is reloaded from list-favs.php, which now returns the real names.
  function promptPrivateUnlock() {
    var $contents = $("#modal-more .contents");
    $contents.find(".private-unlock-form").remove();
    var $form = buildPrivateUnlockForm("Enter the password to see private collections.", "", function() {
      if(window.parent && typeof window.parent.openFavCollectionsPanel === "function") {
        window.parent.openFavCollectionsPanel();
      }
    });
    $contents.prepend($form);
    resizeIframeNow();
    $form.find(".private-unlock-input").trigger("focus");
  }

  // Locks private collections again (ends the session on the server).
  function lockPrivateCollections() {
    $.ajax({
      url: "./private-favs.php?action=logout",
      method: "POST",
      cache: false,
      complete: function() {
        closeFavCollectionsPanel();
        if(isPrivateCollection(currentCollection)) {
          renderPrivateUnlockForm(currentCollection, "Locked.");
        }
      }
    });
  }
  function appendSectionHeader($playlistContainer, sectionCategory, seenCategories, currentGroup) {
    if(!sectionCategory || seenCategories[sectionCategory]) return;
    seenCategories[sectionCategory] = true;

    var sectionId = 'section-' + sectionCategory.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    var $sectionHeader = $('<li class="section-header"></li>');
    var $sectionLabel = $('<span class="section-label"></span>')
      .attr('data-section-id', sectionId)
      .attr('id', sectionId)
      .attr('data-section-label', sectionCategory)
      .attr('data-section-group', currentGroup || '');
    var sectionIcon = (typeof window.playlistIconHtml === "function") ? window.playlistIconHtml(sectionCategory) : "";
    if (sectionIcon) $sectionLabel.append(sectionIcon);
    $sectionLabel.append(document.createTextNode('[' + sectionCategory + ']'));
    $sectionHeader.append($sectionLabel);
    $playlistContainer.append($sectionHeader);
  }

  function renderPlaylists(collection) {
    var $playlistContainer = $(".playlists-target");
    $playlistContainer.html("");

    var seenCategories = {};
    var currentGroup = "";

    for(var i =0; i<collection.length; i++) {
        var playlistObject = collection[i];
        if(!playlistObject || (typeof playlistObject.theme==="string" && typeof playlistObject.name==="undefined" && typeof playlistObject.id==="undefined")) {
          continue; // theme row: styling only, not a list entry
        }
        var groupName = (typeof playlistObject.group==="string") ? playlistObject.group.trim() : "";
        if(groupName) {
          currentGroup = groupName;
          var $groupHeader = $('<li class="section-group"></li>');
          $groupHeader.append($('<h2 class="section-group-label"></h2>').text(currentGroup));
          $playlistContainer.append($groupHeader);
          continue;
        }

        var id = (typeof playlistObject.id!=="undefined")?playlistObject.id:-1;
        var name = playlistObject.name || "";
        var openUrl = (typeof playlistObject.open==="string") ? playlistObject.open.trim() : "";
        var explicitSection = (typeof playlistObject.section==="string") ? playlistObject.section.trim() : "";
        if(explicitSection && id===-1 && !openUrl) {
          appendSectionHeader($playlistContainer, explicitSection, seenCategories, currentGroup);
          continue;
        }

        var categories = extractCategoriesFromPlaylistLabel(name);
        var primaryCategory = categories.length > 0 ? categories[0] : null;
        var sectionCategory = primaryCategory;

        appendSectionHeader($playlistContainer, sectionCategory, seenCategories, currentGroup);

        var li = $("<li></li>")
        var a = $("<a></a>");

        a.attr("href","javascript:void(0);");
        if(openUrl) {
          a.attr("data-open", openUrl);
          a.data("instructions", playlistObject.instructions || "");
          a.attr("onclick", "openPlaylistGuide(event)");
        } else if(id===-1) {
          a.addClass('not-link')
          li.addClass('not-link')
        } else if(id) {
          a.attr("data-playlist-id",id);
          a.attr("onclick", "changeVideo(event)");
        } 
        var playlistIcon = (typeof window.playlistIconHtml === "function") ? window.playlistIconHtml(name) : "";
        a.html(playlistIcon + name);

        li.append(a);
        $playlistContainer.append(li);
      }

      let lastFiltered = localStorage.getItem("YT__last-filtered");
      let $filterInput = $("#playlist-filter");
      if(lastFiltered && lastFiltered.length) {
          $filterInput.val(lastFiltered);
          filterListItems(lastFiltered, $playlistContainer);
      } else {
          syncCategoryBrowser();
      }

      resizeIframeNow();
  } // renderPlaylists

  function resizeIframeNow() {
    window.parent.resizeIframeFavs();
  }

  function closeFavCollectionsPanel() {
    var $panel = $("#modal-more");
    $panel.addClass("fade").attr("aria-hidden", "true");
    resizeIframeNow();
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
  .private-unlock-form {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 10px 0;
  }
  .private-unlock-title {
    flex-basis: 100%;
    margin: 0;
    font-weight: 600;
    color: #5a1a20;
  }
  .private-unlock-input {
    padding: 6px 10px;
    border: 1px solid rgba(188, 35, 46, 0.25);
    border-radius: 8px;
    min-width: 180px;
  }
  .private-unlock-btn,
  .fav-collections-lock-btn {
    padding: 6px 12px;
    border: 1px solid rgba(188, 35, 46, 0.3);
    border-radius: 8px;
    background: #fff;
    color: #8e1823;
    font-weight: 600;
    cursor: pointer;
  }
  .private-unlock-error {
    flex-basis: 100%;
    margin: 0;
    min-height: 1em;
    color: #bc232e;
  }
  .fav-collections-private-actions {
    margin-top: 12px;
    text-align: right;
  }
</style>