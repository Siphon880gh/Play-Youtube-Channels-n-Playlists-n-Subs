<?php
require __DIR__ . '/lib/private-auth.php';

$a = scandir("./favs");
$prependPath = @$_GET["prependPath"];

function removeElement($ele, $haystack) {
    if (($key = array_search($ele, $haystack)) !== FALSE) {
        unset($haystack[$key]);
    }
    return $haystack;
}
$a = removeElement(".DS_Store", $a);
$a = removeElement(".htaccess", $a);
$a = removeElement(".", $a);
$a = removeElement("..", $a);
$a = removeElement("index.php", $a);
$a = removeElement("index.html", $a);
$a = removeElement("Default.json", $a);

// Only read an existing session; never create one just for listing.
yt_private_start_session(true);
$privateUnlocked = yt_private_is_unlocked();
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}
$hasPrivate = false;

echo '<div class="fav-collections-grid">';

echo '<a class="fav-collections-link" href="' . htmlspecialchars($prependPath . 'favs.php', ENT_QUOTES, 'UTF-8') . '">Default</a>';

foreach($a as $link) {
    if (substr($link, -5) !== ".json") continue;
    $collection = substr($link, 0, -5);
    $href = htmlspecialchars($prependPath . 'favs.php?favs=' . rawurlencode($collection), ENT_QUOTES, 'UTF-8');
    $label = htmlspecialchars($collection, ENT_QUOTES, 'UTF-8');
    if (yt_private_is_private_name($collection)) {
        $hasPrivate = true;
        if ($privateUnlocked) {
            echo '<a class="fav-collections-link fav-collections-link--private" href="' . $href . '" title="Private (unlocked)"><i class="fa fa-lock-open" aria-hidden="true"></i> ' . $label . '</a>';
        } else {
            // Locked: the real name never leaves the server. Show random letters instead.
            echo '<a class="fav-collections-link fav-collections-link--private fav-collections-link--locked" href="javascript:void(0);" data-locked="1" onclick="promptPrivateUnlock();" title="Private: enter the password to see it" aria-label="Locked private collection"><i class="fa fa-lock" aria-hidden="true"></i> <span class="fav-collections-scramble" aria-hidden="true">' . yt_private_scramble_label() . '</span></a>';
        }
    } else {
        echo '<a class="fav-collections-link" href="' . $href . '">' . $label . '</a>';
    }
}

echo '</div>';

if ($hasPrivate && $privateUnlocked) {
    echo '<div class="fav-collections-private-actions"><button type="button" class="fav-collections-lock-btn" onclick="lockPrivateCollections();"><i class="fa fa-lock" aria-hidden="true"></i> Lock private collections</button></div>';
}
?>
