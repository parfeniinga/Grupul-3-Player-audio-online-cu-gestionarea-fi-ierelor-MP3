
<?php
    if (!isset($_FILES["fisier"]) || !isset($_POST["nume_melodie"]) || !isset($_POST["gen"])) {
        header("Location: index.php");
        exit;
    }

    $fisier = $_FILES["fisier"];
    $numeMelodie = trim($_POST["nume_melodie"]);
    $gen = $_POST["gen"];

    $genuri = ["Rock", "Pop", "Metal", "Rap", "Hip-Hop", "Jazz", "Electronic", "Clasic", "Altele"];

    if ($numeMelodie === "" || !in_array($gen, $genuri, true)) {
        header("Location: index.php");
        exit;
    }

    if ($fisier["error"] !== UPLOAD_ERR_OK || $fisier["size"] <= 0 || $fisier["size"] > 10 * 1024 * 1024) {
        header("Location: index.php");
        exit;
    }

    if (!is_uploaded_file($fisier["tmp_name"])) {
        header("Location: index.php");
        exit;
    }

    $numeOriginal = basename($fisier["name"]);
    $extensie = strtolower(pathinfo($numeOriginal, PATHINFO_EXTENSION));

    if ($extensie !== "mp3") {
        header("Location: index.php");
        exit;
    }

    $tip = mime_content_type($fisier["tmp_name"]);

    if ($tip !== "audio/mpeg" && $tip !== "audio/mp3" && $tip !== "application/octet-stream") {
        header("Location: index.php");
        exit;
    }

    if (!is_dir("audio")) {
        mkdir("audio", 0755, true);
    }

    $numeFisier = $numeOriginal;
    $cale = "audio/" . $numeFisier;

    if (file_exists($cale)) {
        header("Location: index.php");
        exit;
    }

    if (move_uploaded_file($fisier["tmp_name"], $cale)) {
        $caleDate = "melodii.json";
        $melodii = [];

        if (file_exists($caleDate)) {
            $continut = file_get_contents($caleDate);
            $melodii = json_decode($continut, true);

            if (!is_array($melodii)) {
                $melodii = [];
            }
        }

        $melodii[$numeFisier] = [
            "nume" => $numeMelodie,
            "gen" => $gen
        ];

        file_put_contents(
            $caleDate,
            json_encode($melodii, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    header("Location: index.php");
    exit;
?>
