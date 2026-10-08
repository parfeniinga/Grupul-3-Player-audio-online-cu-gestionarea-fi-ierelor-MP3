<?php
$fisiere = scandir("audio");
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3 Player</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto px-4 py-8">

        <h1 class="text-4xl font-bold text-center text-gray-800 mb-2">
            MP3 Player
        </h1>

        <p class="text-center text-gray-600 mb-8">
            Player audio online
        </p>

        <div class="bg-white rounded-xl shadow-md p-6 mb-8">

            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Încarcă un fișier MP3
            </h2>

            <form action="upload.php" method="POST" enctype="multipart/form-data">

                <input
                    type="file"
                    name="fisier"
                    accept=".mp3,audio/mpeg"
                    class="block w-full border border-gray-300 rounded-lg p-3 mb-4"
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

                if ($extensie === "mp3") {

                    $existaFisier = true;
                    ?>

                    <div class="border border-gray-200 rounded-lg p-4 mb-4">

                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($fisier); ?>
                        </h3>

                        <p class="text-sm text-gray-500 mb-3">
                            Durata: <span class="durata">Se calculează...</span>
                        </p>

                        <audio controls class="w-full player">
                            <source
                                src="audio/<?php echo rawurlencode($fisier); ?>"
                                type="audio/mpeg"
                            >
                        </audio>
                        <a
                            href="download.php?fisier=<?php echo rawurlencode($fisier); ?>"
                            class="inline-block mt-3 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg"
                        >
                            Descarcă
                        </a>

                    </div>

                    <?php
                }
            }

            if (!$existaFisier) {
                echo '<p class="text-gray-500">Nu există încă fișiere audio.</p>';
            }
            ?>

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