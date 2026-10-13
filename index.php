<?php
$fisiere = scandir("audio");
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3 Player</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="w-full max-w-5xl mx-auto px-3 sm:px-4 py-5 sm:py-8">

        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center text-gray-800 mb-2">
            MP3 Player
        </h1>

        <p class="text-center text-gray-600 mb-8">
            Player audio online
        </p>

        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-6 sm:mb-8">

            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Încarcă un fișier MP3
            </h2>

            <form action="upload.php" method="POST" enctype="multipart/form-data">

                <input
                    type="file"
                    name="fisiere[]"
                    accept=".mp3,audio/mpeg"
                    class="block w-full border border-gray-300 rounded-lg p-3 mb-4 bg-white text-gray-800"
                    multiple
                    required
                >

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg"
                >
                    Încarcă MP3
                </button>

            </form>

            <p class="text-sm text-gray-500 mt-3">
                Sunt acceptate doar fișiere MP3 de maximum 10 MB.
            </p>

        </div>

        <div class="bg-white rounded-xl shadow-md p-6">

            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Fișiere audio
            </h2>

            <?php
            $existaFisier = false;

            foreach ($fisiere as $fisier) {

                $extensie = strtolower(pathinfo($fisier, PATHINFO_EXTENSION));

                if ($extensie === "mp3" && is_file("audio/" . $fisier)) {

                    $existaFisier = true;
                    $dimensiune = round(filesize("audio/" . $fisier) / (1024 * 1024), 2);
                    ?>

                    <div class="border border-gray-200 rounded-lg p-4 mb-4">

                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($fisier, ENT_QUOTES, 'UTF-8'); ?>
                        </h3>

                        <p class="text-sm text-gray-500 mb-3">
                            Durata: <span class="durata">Se calculează...</span>
                            <span class="mx-2">|</span>
                            Dimensiune: <?php echo $dimensiune; ?> MB
                        </p>

                        <audio controls preload="metadata" class="w-full player">
                            <source
                                src="audio/<?php echo rawurlencode($fisier); ?>"
                                type="audio/mpeg"
                            >
                            Browserul tău nu acceptă redarea audio.
                        </audio>

                        <div class="flex flex-wrap items-center gap-3 mt-3">

                            <a
                                href="download.php?fisier=<?php echo rawurlencode($fisier); ?>"
                                class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg"
                            >
                                Descarcă
                            </a>

                            <form
                                action="delete.php"
                                method="POST"
                                onsubmit="return confirm('Sigur vrei sa stergi acest fisier?');"
                            >
                                <input
                                    type="hidden"
                                    name="fisier"
                                    value="<?php echo htmlspecialchars($fisier, ENT_QUOTES, 'UTF-8'); ?>"
                                >

                                <button
                                    type="submit"
                                    class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700"
                                >
                                    Șterge
                                </button>
                            </form>

                        </div>

                    </div>

                    <?php
                }
            }

            if (!$existaFisier) {
                echo '<p class="text-gray-500">Nu există încă fișiere audio.</p>';
            }
            ?>

        </div>

        <div class="text-center mt-8 mb-8">
            <a
                href="lista.php"
                class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-lg"
            >
                Deschide lista
            </a>
        </div>

    </div>

    <script>
        const playere = document.querySelectorAll(".player");

        playere.forEach(function(player) {

            player.addEventListener("loadedmetadata", function() {

                const durata = Math.floor(player.duration);
                const minute = Math.floor(durata / 60);
                const secunde = durata % 60;

                let secundeAfisate = secunde;

                if (secunde < 10) {
                    secundeAfisate = "0" + secunde;
                }

                player.parentElement.querySelector(".durata").textContent =
                    minute + ":" + secundeAfisate;
            });

        });
    </script>

</body>
</html>