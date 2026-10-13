
<?php
$fisiere = is_dir("audio") ? scandir("audio") : [];
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3 Player</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#f5f2fa] text-[#383044]">

    <div class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 sm:py-14">

        <header class="mb-10 text-center">

            <h1 class="text-4xl font-bold tracking-tight text-[#504064] sm:text-5xl">
                MP3 Player
            </h1>

            <p class="mt-3 text-sm text-[#8b819b] sm:text-base">
                Player audio online
            </p>

            <div class="mx-auto mt-5 h-1 w-16 rounded-full bg-[#d8c9ed]"></div>

        </header>

        <section class="mb-8 rounded-2xl border border-[#e9e1f2] bg-white p-5 shadow-sm sm:p-7">

            <div class="mb-5">
                <h2 class="text-xl font-semibold text-[#504064] sm:text-2xl">
                    Încarcă un fișier MP3
                </h2>

                <p class="mt-2 text-sm text-[#92879f]">
                    Adaugă melodiile preferate în colecția ta.
                </p>
            </div>

            <form action="upload.php" method="POST" enctype="multipart/form-data">

                <input
                    type="file"
                    name="fisiere[]"
                    accept=".mp3,audio/mpeg"
                    multiple
                    required
                    class="mb-4 block w-full rounded-xl border border-[#e5dced] bg-[#fcfaff] p-3 text-sm text-[#62556f] file:mr-3 file:rounded-lg file:border-0 file:bg-[#eee6f7] file:px-4 file:py-2 file:font-medium file:text-[#6e5888] hover:file:bg-[#e4d8f2] focus:border-[#c9b5e3] focus:outline-none"
                >

                <button
                    type="submit"
                    class="rounded-xl bg-[#d8c8ec] px-6 py-3 font-semibold text-[#514064] transition hover:bg-[#cbb6e5]"
                >
                    Încarcă MP3
                </button>

            </form>

            <p class="mt-3 text-xs text-[#a096ac]">
                Sunt acceptate fișiere MP3 de maximum 10 MB fiecare.
            </p>

        </section>

        <section class="rounded-2xl border border-[#e9e1f2] bg-white p-5 shadow-sm sm:p-7">

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-[#504064] sm:text-2xl">
                    Lista melodiilor
                </h2>

                <p class="mt-2 text-sm text-[#92879f]">
                    Toate fișierele audio într-un singur loc.
                </p>
            </div>

            <?php
            $existaFisier = false;

            foreach ($fisiere as $fisier) {

                $extensie = strtolower(pathinfo($fisier, PATHINFO_EXTENSION));

                if ($extensie === "mp3" && is_file("audio/" . $fisier)) {

                    $existaFisier = true;
                    $dimensiune = round(filesize("audio/" . $fisier) / (1024 * 1024), 2);
                    ?>

                    <div class="mb-4 rounded-xl border border-[#eee7f4] bg-[#fcfaff] p-4 transition hover:border-[#d4c2e8] sm:p-5">

                        <h3 class="break-words text-base font-semibold text-[#514064] sm:text-lg">
                            <?php echo htmlspecialchars($fisier, ENT_QUOTES, 'UTF-8'); ?>
                        </h3>

                        <p class="mb-4 mt-2 text-xs text-[#968ba2] sm:text-sm">
                            Durata:
                            <span class="durata text-[#8d76aa]">Se calculează...</span>

                            <span class="mx-2">|</span>

                            Dimensiune:
                            <?php echo $dimensiune; ?> MB
                        </p>

                        <audio
                            controls
                            preload="metadata"
                            class="player w-full"
                        >
                            <source
                                src="audio/<?php echo rawurlencode($fisier); ?>"
                                type="audio/mpeg"
                            >

                            Browserul tău nu acceptă redarea audio.
                        </audio>

                        <div class="mt-4 flex flex-wrap items-center gap-3">

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

            if (!$existaFisier) {
                ?>

                <div class="rounded-xl border border-dashed border-[#ded3e9] bg-[#fcfaff] px-4 py-10 text-center">

                    <p class="text-lg font-medium text-[#6d5b80]">
                        Lista este încă goală
                    </p>

                    <p class="mt-2 text-sm text-[#a096ac]">
                        Încarcă primul fișier MP3 pentru a începe.
                    </p>

                </div>

                <?php
            }
            ?>

        </section>

        <div class="mt-7 text-center">

            <a
                href="lista.php"
                class="inline-block rounded-xl border border-[#dcd0e9] bg-[#eee7f6] px-6 py-3 font-semibold text-[#68547f] transition hover:bg-[#e3d8f0]"
            >
                Deschide lista
            </a>

        </div>

        <footer class="mt-10 border-t border-[#e6deef] pt-5 text-center text-xs text-[#a096ac]">
            MP3 Player · Player audio online
        </footer>

    </div>

    <script>
        const playere = document.querySelectorAll(".player");

        playere.forEach(function(player) {

            player.addEventListener("loadedmetadata", function() {

                const durata = Math.floor(player.duration);

                if (!Number.isFinite(durata)) {
                    return;
                }

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
