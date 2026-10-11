
<?php
    if (!isset($_FILES["fisiere"])) {
        header("Location: index.php");
        exit;
    }

    $fisiere = $_FILES["fisiere"];

    if (!is_dir("audio")) {
        mkdir("audio", 0755, true);
    }

    $total = count($fisiere["name"]);

    for ($i = 0; $i < $total; $i++) {
        if ($fisiere["error"][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        if ($fisiere["size"][$i] <= 0 || $fisiere["size"][$i] > 10 * 1024 * 1024) {
            continue;
        }

        if (!is_uploaded_file($fisiere["tmp_name"][$i])) {
            continue;
        }

        $numeOriginal = basename($fisiere["name"][$i]);
        $extensie = strtolower(pathinfo($numeOriginal, PATHINFO_EXTENSION));

        if ($extensie !== "mp3") {
            continue;
        }

        $tip = mime_content_type($fisiere["tmp_name"][$i]);

        if ($tip !== "audio/mpeg" && $tip !== "audio/mp3" && $tip !== "application/octet-stream") {
            continue;
        }

        $nume = uniqid("melodie_", true) . ".mp3";
        $cale = "audio/" . $nume;

        move_uploaded_file($fisiere["tmp_name"][$i], $cale);
    }

    header("Location: index.php");
    exit;
?>
