<?php

    if (!isset($_GET["fisier"])) {
        header("Location: index.php");
        exit;
    }

    $nume = basename($_GET["fisier"]);
    $cale = "audio/" . $nume;

    if (!file_exists($cale)) {
        header("Location: index.php");
        exit;
    }

    header("Content-Type: audio/mpeg");
    header("Content-Disposition: attachment; filename=\"" . $nume . "\"");
    header("Content-Length: " . filesize($cale));

    readfile($cale);
    exit;
?>