<?php
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

echo '<div class="fav-collections-grid">';

echo '<a class="fav-collections-link" href="' . htmlspecialchars($prependPath . 'favs.php', ENT_QUOTES, 'UTF-8') . '">Default</a>';

foreach($a as $link) {
    $collection = str_replace(".json", "", $link);
    $href = htmlspecialchars($prependPath . 'favs.php?favs=' . rawurlencode($collection), ENT_QUOTES, 'UTF-8');
    $label = htmlspecialchars($collection, ENT_QUOTES, 'UTF-8');
    echo '<a class="fav-collections-link" href="' . $href . '">' . $label . '</a>';
}

echo '</div>';
?>
