<?php

function redirect($loc = "."){
    header("HTTP/1.1 302 Found");
    header("Location: " . $loc);
    exit();
}

?>
