
<?php
$fisiere = is_dir("audio") ? scandir("audio") : [];
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista pieselor audio</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#f5f2fa] text-[#383044]">

    <div class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 sm:py-14">

        <header class="mb-8 text-center">

            <h1 class="text-3xl font-bold tracking-tight text-[#504064] sm:text-5xl">
                Lista pieselor audio
            </h1>

            <p class="mt-3 text-sm text-[#8b819b] sm:text-base">
                Colecția ta de muzică
            </p>

            <div class="mx-auto mt-5 h-1 w-16 rounded-full bg-[#d8c9ed]"></div>

        </header>

        <div class="mb-6 text-center">
            <a
                href="index.php"
                class="inline-block rounded-xl bg-[#d8c8ec] px-5 py-3 font-semibold text-[#514064] transition hover:bg-[#cbb6e5]"
            >
                Înapoi la pagina principală
            </a>
        </div>

        <section class="rounded-2xl border border-[#e9e1f2] bg-white p-5 shadow-sm sm:p-7">

            <h2 class="mb-2 text-xl font-semibold text-[#504064] sm:text-2xl">
                Melodiile tale
            </h2>

            <p class="mb-6 text-sm text-[#92879f]">
                Ascultă sau descarcă piesele preferate.
            </p>

            <?php
            $numar = 0;

            foreach ($fisiere as $fisier) {

                if (
                    strtolower(pathinfo($fisier, PATHINFO_EXTENSION)) === "mp3"
                    && is_file("audio/" . $fisier)
                ) {

                    $numar++;
                    $cale = "audio/" . $fisier;
                    $dimensiune = round(filesize($cale) / (1024 * 1024), 2);
                    ?>

                    <div class="mb-4 rounded-xl border border-[#eee7f4] bg-[#fcfaff] p-4 transition hover:border-[#d4c2e8] sm:p-5">

                        <h3 class="break-words text-base font-semibold text-[#514064] sm:text-lg">
                            <?php echo htmlspecialchars($fisier, ENT_QUOTES, 'UTF-8'); ?>
                        </h3>

                        <p class="mb-4 mt-2 text-sm text-[#968ba2]">
                            Dimensiune:
                            <span class="text-[#79678f]">
                                <?php echo $dimensiune; ?> MB
                            </span>
                        </p>

                        <audio controls preload="metadata" class="mb-4 w-full">
                            <source
                                src="audio/<?php echo rawurlencode($fisier); ?>"
                                type="audio/mpeg"
                            >
                            Browserul tău nu acceptă redarea audio.
                        </audio>

                        <div class="flex flex-wrap gap-3">

                            <a
                                href="download.php?fisier=<?php echo rawurlencode($fisier); ?>"
                                class="inline-block rounded-lg bg-[#e5d9f2] px-4 py-2.5 text-sm font-semibold text-[#604b7a] transition hover:bg-[#d8c7eb]"
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
                                    class="rounded-lg border border-[#ead7dd] bg-[#fbf0f3] px-4 py-2.5 text-sm font-semibold text-[#a36c7d] transition hover:bg-[#f5e2e8]"
                                >
                                    Șterge
                                </button>

                            </form>

                        </div>

                    </div>

                    <?php
                }
            }

            if ($numar === 0) {
                ?>

                <div class="rounded-xl border border-dashed border-[#ded3e9] bg-[#fcfaff] px-4 py-10 text-center">

                    <p class="text-lg font-medium text-[#6d5b80]">
                        Nu există piese MP3 încărcate.
                    </p>

                    <p class="mt-2 text-sm text-[#a096ac]">
                        Încarcă fișiere MP3 din pagina principală.
                    </p>

                </div>

                <?php
            }
            ?>

        </section>

        <footer class="mt-10 border-t border-[#e6deef] pt-5 text-center text-xs text-[#a096ac]">
            MP3 Player · Player audio online
        </footer>

    </div>

</body>
</html>
