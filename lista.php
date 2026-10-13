
<?php
$fisiere = is_dir("audio") ? scandir("audio") : [];
$melodii = [];
$playlisturi = [];

if (file_exists("melodii.json")) {
    $continut = file_get_contents("melodii.json");
    $date = json_decode($continut, true);

    if (is_array($date)) {
        $melodii = $date;
    }
}

foreach ($fisiere as $fisier) {
    if (
        strtolower(pathinfo($fisier, PATHINFO_EXTENSION)) === "mp3"
        && is_file("audio/" . $fisier)
    ) {
        $nume = isset($melodii[$fisier]["nume"])
            ? $melodii[$fisier]["nume"]
            : pathinfo($fisier, PATHINFO_FILENAME);

        $gen = isset($melodii[$fisier]["gen"])
            ? $melodii[$fisier]["gen"]
            : "Fără gen";

        if (!isset($playlisturi[$gen])) {
            $playlisturi[$gen] = [];
        }

        $playlisturi[$gen][] = [
            "fisier" => $fisier,
            "nume" => $nume,
            "gen" => $gen
        ];
    }
}

ksort($playlisturi);
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playlisturile mele</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#f5f2fa] text-[#383044]">

    <div class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 sm:py-14">

        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-[#504064] sm:text-5xl">
                Playlisturile mele
            </h1>

            <p class="mt-3 text-sm text-[#8b819b] sm:text-base">
                Muzica organizată după gen
            </p>

            <div class="mx-auto mt-5 h-1 w-16 rounded-full bg-[#d8c9ed]"></div>
        </header>

        <div class="mb-8 flex flex-wrap justify-center gap-3">

            <a
                href="playlisturi.php"
                class="inline-block rounded-xl border border-[#dcd0e9] bg-[#eee7f6] px-6 py-3 font-semibold text-[#68547f] transition hover:bg-[#e3d8f0]"
            >
                Revino la pagina principală
            </a>

        </div>

        <?php if (count($playlisturi) === 0) { ?>

            <section class="rounded-2xl border border-[#e9e1f2] bg-white p-5 shadow-sm sm:p-7">
                <div class="rounded-xl border border-dashed border-[#ded3e9] bg-[#fcfaff] px-4 py-10 text-center">

                    <p class="text-lg font-medium text-[#6d5b80]">
                        Nu există playlisturi încă
                    </p>

                    <p class="mt-2 text-sm text-[#a096ac]">
                        Încarcă o melodie și alege genul muzical pentru a crea primul playlist.
                    </p>

                </div>
            </section>

        <?php } else { ?>

            <div class="mb-6 rounded-xl border border-[#e9e1f2] bg-white p-4 text-sm text-[#79678f]">
                Număr de genuri muzicale:
                <span class="font-semibold"><?php echo count($playlisturi); ?></span>
            </div>

            <?php foreach ($playlisturi as $gen => $listaMelodii) { ?>

                <section class="mb-7 rounded-2xl border border-[#e9e1f2] bg-white p-5 shadow-sm sm:p-7">

                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">

                        <div>
                            <h2 class="text-xl font-semibold text-[#504064] sm:text-2xl">
                                <?php echo htmlspecialchars($gen, ENT_QUOTES, 'UTF-8'); ?>
                            </h2>

                            <p class="mt-2 text-sm text-[#92879f]">
                                Playlist cu melodii din genul
                                <?php echo htmlspecialchars($gen, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>

                        <span class="rounded-lg bg-[#eee6f7] px-3 py-2 text-sm font-medium text-[#6e5888]">
                            <?php echo count($listaMelodii); ?>
                            <?php echo count($listaMelodii) === 1 ? 'melodie' : 'melodii'; ?>
                        </span>

                    </div>

                    <?php foreach ($listaMelodii as $melodie) {
                        $fisier = $melodie["fisier"];
                        $cale = "audio/" . $fisier;
                        $dimensiune = round(filesize($cale) / (1024 * 1024), 2);
                    ?>

                        <div class="mb-4 rounded-xl border border-[#eee7f4] bg-[#fcfaff] p-4 transition hover:border-[#d4c2e8] sm:p-5">

                            <h3 class="break-words text-base font-semibold text-[#514064] sm:text-lg">
                                <?php echo htmlspecialchars($melodie["nume"], ENT_QUOTES, 'UTF-8'); ?>
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

                    <?php } ?>

                </section>

            <?php } ?>

        <?php } ?>

        <footer class="mt-10 border-t border-[#e6deef] pt-5 text-center text-xs text-[#a096ac]">
            MP3 Player · Playlisturi muzicale
        </footer>

    </div>

</body>
</html>
