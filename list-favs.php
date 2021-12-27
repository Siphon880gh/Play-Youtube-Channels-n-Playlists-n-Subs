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
$a = removeElement("default.json", $a);

echo "
<div class='fav-collections'>
<b>More favs collections:</b><br/><br/>
<a href=\"" . $prependPath . "favs.php\">[Default]</a>";

foreach($a as $link) {
    $collection = str_replace(".json", "", $link);
    echo "
<a href=\"" . $prependPath . "favs.php?favs=$collection\">$collection</a>
    ";
}
echo "</div>";
?>