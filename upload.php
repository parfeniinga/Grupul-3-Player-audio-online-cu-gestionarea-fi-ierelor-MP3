<?php
    if (!isset($_FILES["fisier"])) {
        header("Location: index.php");
        exit;
    }

    $fisier = $_FILES["fisier"];

    if ($fisier["error"] !== UPLOAD_ERR_OK) {
        header("Location: index.php");
        exit;
    }

    if ($fisier["size"] > 10 * 1024 * 1024) {
        header("Location: index.php");
        exit;
    }

    $nume = basename($fisier["name"]);
    $extensie = strtolower(pathinfo($nume, PATHINFO_EXTENSION));

    if ($extensie !== "mp3") {
        header("Location: index.php");
        exit;
    }

    $cale = "audio/" . $nume;

    if (file_exists($cale)) {
        header("Location: index.php");
        exit;
    }

    if (move_uploaded_file($fisier["tmp_name"], $cale)) {
        header("Location: index.php");
        exit;
    }

    header("Location: index.php");
    exit;
?>