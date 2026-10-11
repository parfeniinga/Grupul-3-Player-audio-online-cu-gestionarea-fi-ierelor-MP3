
<?php
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["fisier"])) {
        header("Location: index.php");
        exit;
    }

    $nume = basename($_POST["fisier"]);
    $extensie = strtolower(pathinfo($nume, PATHINFO_EXTENSION));
    $cale = "audio/" . $nume;

    if ($extensie === "mp3" && is_file($cale) && !is_link($cale)) {
        unlink($cale);
    }

    header("Location: index.php");
    exit;
?>
