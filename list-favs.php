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

echo "
<a href=\"" . $prependPath . "favs.php\">favs.php</a>
    ";
foreach($a as $link) {
    echo "
<a href=\"" . $prependPath . "favs/$link\">$link</a>
    ";
}
?>