<?php

$fisiere = scandir("audio");

if (!file_exists("audio/playlist.json")) {
    file_put_contents("audio/playlist.json", "[]");
}

$playlist = json_decode(file_get_contents("audio/playlist.json"), true);

if (isset($_GET["adauga"])) {

    $nume = basename($_GET["adauga"]);

    if (file_exists("audio/" . $nume) && !in_array($nume, $playlist)) {
        $playlist[] = $nume;
        file_put_contents("audio/playlist.json", json_encode($playlist));
    }

    header("Location: playlist.php");
    exit;
}

if (isset($_GET["sterge"])) {

    $nume = basename($_GET["sterge"]);

    $nouPlaylist = [];

    foreach ($playlist as $melodie) {
        if ($melodie !== $nume) {
            $nouPlaylist[] = $melodie;
        }
    }

    file_put_contents("audio/playlist.json", json_encode($nouPlaylist));

    header("Location: playlist.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ro">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Playlist MP3</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto px-4 py-8">

        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">
            Playlist
        </h1>

        <div class="bg-white rounded-xl shadow-md p-6 mb-8">

            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Adaugă melodii
            </h2>

            <?php

            foreach ($fisiere as $fisier) {

                if (strtolower(pathinfo($fisier, PATHINFO_EXTENSION)) === "mp3") {
                    ?>

                    <div class="flex justify-between items-center border-b py-3">

                        <span>
                            <?php echo htmlspecialchars($fisier); ?>
                        </span>

                        <a
                            href="playlist.php?adauga=<?php echo rawurlencode($fisier); ?>"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
                        >
                            Adaugă
                        </a>

                    </div>

                    <?php
                }
            }

            ?>

        </div>

        <div class="bg-white rounded-xl shadow-md p-6">

            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Playlist-ul meu
            </h2>

            <?php

            if (count($playlist) === 0) {

                echo '<p class="text-gray-500">Playlist-ul este gol.</p>';

            } else {

                foreach ($playlist as $melodie) {
                    ?>

                    <div class="border border-gray-200 rounded-lg p-4 mb-4">

                        <h3 class="font-semibold text-gray-800 mb-3">
                            <?php echo htmlspecialchars($melodie); ?>
                        </h3>

                        <audio controls class="w-full mb-3">
                            <source
                                src="audio/<?php echo rawurlencode($melodie); ?>"
                                type="audio/mpeg"
                            >
                        </audio>

                        <a
                            href="playlist.php?sterge=<?php echo rawurlencode($melodie); ?>"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg"
                        >
                            Șterge din playlist
                        </a>

                    </div>

                    <?php
                }
            }

            ?>

        </div>

    </div>

</body>

</html>