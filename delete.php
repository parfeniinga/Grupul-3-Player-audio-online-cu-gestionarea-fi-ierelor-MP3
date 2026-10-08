<?php

    if (!isset($_GET["fisier"])) {
        header("Location: index.php");
        exit;
    }

    $nume = basename($_GET["fisier"]);
    $cale = "audio/" . $nume;

    if (file_exists($cale)) {
        unlink($cale);
    }

    header("Location: index.php");
    exit;
?>