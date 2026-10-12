
<?php
$fisiere = scandir("audio");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista pieselor audio</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">
            Lista pieselor audio
        </h1>
        <div class="text-center mb-6">
            <a href="index.php"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg">
                Înapoi la pagina principală
            </a>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <?php
            $numar = 0;
            foreach ($fisiere as $fisier) {
                if (strtolower(pathinfo($fisier, PATHINFO_EXTENSION)) === "mp3" && is_file("audio/" . $fisier)) {
                    $numar++;
                    $cale = "audio/" . $fisier;
                    $dimensiune = round(filesize($cale) / (1024 * 1024), 2);
                    ?>
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($fisier, ENT_QUOTES, 'UTF-8'); ?>
                        </h2>
                        <p class="text-sm text-gray-500 mb-3">
                            Dimensiune: <?php echo $dimensiune; ?> MB
                        </p>
                        <audio controls preload="metadata" class="w-full mb-3">
                            <source src="audio/<?php echo rawurlencode($fisier); ?>" type="audio/mpeg">
                            Browserul tău nu acceptă redarea audio.
                        </audio>
                        <div class="flex flex-wrap gap-3">
                            <a href="download.php?fisier=<?php echo rawurlencode($fisier); ?>"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                Descarcă
                            </a>
                            <form action="delete.php" method="POST"
                                  onsubmit="return confirm('Sigur vrei sa stergi acest fisier?');">
                                <input type="hidden" name="fisier"
                                       value="<?php echo htmlspecialchars($fisier, ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                    Șterge
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            }

            if ($numar === 0) {
                echo '<p class="text-center text-gray-500">Nu există piese MP3 încărcate.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
