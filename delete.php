
<?php
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["fisier"])) {
        header("Location: index.php");
        exit;
    }

    $nume = basename($_POST["fisier"]);
    $extensie = strtolower(pathinfo($nume, PATHINFO_EXTENSION));
    $cale = "audio/" . $nume;

    if ($extensie === "mp3" && is_file($cale) && !is_link($cale)) {
        if (unlink($cale)) {
            if (file_exists("melodii.json")) {
                $melodii = json_decode(file_get_contents("melodii.json"), true);

                if (is_array($melodii) && array_key_exists($nume, $melodii)) {
                    unset($melodii[$nume]);

                    file_put_contents(
                        "melodii.json",
                        json_encode($melodii, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        LOCK_EX
                    );
                }
            }

            if (file_exists("playlisturi.json")) {
                $playlisturi = json_decode(file_get_contents("playlisturi.json"), true);

                if (is_array($playlisturi)) {
                    foreach ($playlisturi as $id => $playlist) {
                        if (isset($playlist["piese"]) && is_array($playlist["piese"])) {
                            $playlisturi[$id]["piese"] = array_values(
                                array_filter(
                                    $playlist["piese"],
                                    function ($piesa) use ($nume) {
                                        return $piesa !== $nume;
                                    }
                                )
                            );
                        }
                    }

                    file_put_contents(
                        "playlisturi.json",
                        json_encode($playlisturi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        LOCK_EX
                    );
                }
            }
        }
    }

    header("Location: index.php");
    exit;
?>
