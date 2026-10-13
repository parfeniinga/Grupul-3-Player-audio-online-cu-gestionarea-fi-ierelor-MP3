
<?php
    $caleAudio = "audio/";

    if (!is_dir($caleAudio)) {
        mkdir($caleAudio, 0777, true);
    }

    $melodii = [];

    foreach (scandir($caleAudio) as $fisier) {
        if ($fisier !== "." && $fisier !== ".." &&
            strtolower(pathinfo($fisier, PATHINFO_EXTENSION)) === "mp3" &&
            is_file($caleAudio . $fisier) &&
            !is_link($caleAudio . $fisier)) {
            $melodii[] = $fisier;
        }
    }

    $metadateMelodii = [];

    if (file_exists("melodii.json")) {
        $dateMelodii = json_decode(file_get_contents("melodii.json"), true);

        if (is_array($dateMelodii)) {
            $metadateMelodii = $dateMelodii;
        }
    }

    $playlisturi = [];

    if (file_exists("playlisturi.json")) {
        $datePlaylisturi = json_decode(file_get_contents("playlisturi.json"), true);

        if (is_array($datePlaylisturi)) {
            $playlisturi = $datePlaylisturi;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actiune"])) {
        $actiune = $_POST["actiune"];

        if ($actiune === "creeaza") {
            $numePlaylist = trim($_POST["nume_playlist"] ?? "");
            $pieseSelectate = $_POST["piese"] ?? [];
            $pieseValide = [];

            if (is_array($pieseSelectate)) {
                foreach ($pieseSelectate as $piesa) {
                    $piesa = basename($piesa);

                    if (in_array($piesa, $melodii, true)) {
                        $pieseValide[] = $piesa;
                    }
                }
            }

            if ($numePlaylist !== "" && strlen($numePlaylist) <= 100) {
                $idPlaylist = uniqid("playlist_");

                $playlisturi[$idPlaylist] = [
                    "nume" => $numePlaylist,
                    "piese" => array_values(array_unique($pieseValide))
                ];

                file_put_contents(
                    "playlisturi.json",
                    json_encode($playlisturi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    LOCK_EX
                );
            }
        }

        if ($actiune === "adauga") {
            $idPlaylist = $_POST["id_playlist"] ?? "";
            $pieseSelectate = $_POST["piese"] ?? [];

            if (isset($playlisturi[$idPlaylist]) && is_array($pieseSelectate)) {
                if (!isset($playlisturi[$idPlaylist]["piese"]) ||
                    !is_array($playlisturi[$idPlaylist]["piese"])) {
                    $playlisturi[$idPlaylist]["piese"] = [];
                }

                foreach ($pieseSelectate as $piesa) {
                    $piesa = basename($piesa);

                    if (in_array($piesa, $melodii, true) &&
                        !in_array($piesa, $playlisturi[$idPlaylist]["piese"], true)) {
                        $playlisturi[$idPlaylist]["piese"][] = $piesa;
                    }
                }

                file_put_contents(
                    "playlisturi.json",
                    json_encode($playlisturi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    LOCK_EX
                );
            }
        }

        if ($actiune === "elimina_piesa") {
            $idPlaylist = $_POST["id_playlist"] ?? "";
            $piesa = basename($_POST["piesa"] ?? "");

            if (isset($playlisturi[$idPlaylist]) &&
                isset($playlisturi[$idPlaylist]["piese"]) &&
                is_array($playlisturi[$idPlaylist]["piese"])) {

                $playlisturi[$idPlaylist]["piese"] = array_values(
                    array_filter(
                        $playlisturi[$idPlaylist]["piese"],
                        function ($fisier) use ($piesa) {
                            return $fisier !== $piesa;
                        }
                    )
                );

                file_put_contents(
                    "playlisturi.json",
                    json_encode($playlisturi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    LOCK_EX
                );
            }
        }

        if ($actiune === "sterge") {
            $idPlaylist = $_POST["id_playlist"] ?? "";

            if (isset($playlisturi[$idPlaylist])) {
                unset($playlisturi[$idPlaylist]);

                file_put_contents(
                    "playlisturi.json",
                    json_encode($playlisturi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    LOCK_EX
                );
            }
        }

        header("Location: playlisturi.php");
        exit;
    }

    function textSigur($text) {
        return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
    }

    function numeMelodie($fisier, $metadateMelodii) {
        if (isset($metadateMelodii[$fisier]["nume"]) &&
            $metadateMelodii[$fisier]["nume"] !== "") {
            return $metadateMelodii[$fisier]["nume"];
        }

        return pathinfo($fisier, PATHINFO_FILENAME);
    }
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playlisturile mele - MP3 Player</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-[#f5f2fa] text-[#383044]">
    <main class="mx-auto max-w-5xl px-4 py-10">
        <header class="mb-8">
            <a href="index.php" class="text-sm font-semibold text-[#806b98] hover:text-[#68547f]">
                Înapoi la MP3 Player
            </a>
            <h1 class="mt-4 text-3xl font-bold">Playlisturile mele</h1>
            <p class="mt-2 text-[#776b84]">
                Creează playlisturi proprii și organizează melodiile preferate.
            </p>
        </header>

        <section class="mb-8 rounded-2xl border border-[#e5dced] bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-bold">Creează un playlist</h2>

            <form method="POST" action="playlisturi.php">
                <input type="hidden" name="actiune" value="creeaza">

                <label for="nume_playlist" class="mb-2 block font-semibold">
                    Denumirea playlistului
                </label>
                <input
                    type="text"
                    id="nume_playlist"
                    name="nume_playlist"
                    maxlength="100"
                    required
                    placeholder="Ex.: Melodiile mele preferate"
                    class="mb-5 w-full rounded-xl border border-[#dcd0e9] bg-[#fcfaff] px-4 py-3 outline-none focus:border-[#a58abc]"
                >

                <h3 class="mb-3 font-semibold">Alege melodiile</h3>

                <?php if (count($melodii) > 0): ?>
                    <div class="mb-5 grid gap-3 sm:grid-cols-2">
                        <?php foreach ($melodii as $fisier): ?>
                            <label class="flex items-start gap-3 rounded-xl border border-[#e8e0f0] bg-[#fcfaff] p-3">
                                <input
                                    type="checkbox"
                                    name="piese[]"
                                    value="<?php echo textSigur($fisier); ?>"
                                    class="mt-1 accent-[#806b98]"
                                >
                                <span class="break-words">
                                    <?php echo textSigur(numeMelodie($fisier, $metadateMelodii)); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-5 text-[#776b84]">
                        Nu există melodii. Adaugă mai întâi fișiere MP3 din pagina principală.
                    </p>
                <?php endif; ?>

                <button
                    type="submit"
                    class="rounded-xl bg-[#e9e0f2] px-5 py-3 font-semibold text-[#68547f] transition hover:bg-[#ded0ec]"
                >
                    Creează playlistul
                </button>
            </form>
        </section>

        <section>
            <h2 class="mb-4 text-xl font-bold">Playlisturile salvate</h2>

            <?php if (count($playlisturi) === 0): ?>
                <div class="rounded-2xl border border-[#e5dced] bg-white p-6 text-[#776b84]">
                    Nu ai creat încă niciun playlist.
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($playlisturi as $id => $playlist): ?>
                        <article class="rounded-2xl border border-[#e5dced] bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-bold">
                                        <?php echo textSigur($playlist["nume"] ?? "Playlist fără nume"); ?>
                                    </h3>
                                    <p class="mt-1 text-sm text-[#776b84]">
                                        <?php
                                            $piesePlaylist = isset($playlist["piese"]) && is_array($playlist["piese"])
                                                ? $playlist["piese"]
                                                : [];
                                            echo count($piesePlaylist);
                                            echo count($piesePlaylist) === 1 ? " piesă" : " piese";
                                        ?>
                                    </p>
                                </div>

                                <form method="POST" action="playlisturi.php" onsubmit="return confirm('Sigur vrei să ștergi acest playlist?');">
                                    <input type="hidden" name="actiune" value="sterge">
                                    <input type="hidden" name="id_playlist" value="<?php echo textSigur($id); ?>">
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-[#f4e8ef] px-4 py-2 text-sm font-semibold text-[#8b526d] transition hover:bg-[#ecd9e5]"
                                    >
                                        Șterge playlistul
                                    </button>
                                </form>
                            </div>

                            <?php if (count($piesePlaylist) > 0): ?>
                                <div class="mt-5 space-y-4">
                                    <?php foreach ($piesePlaylist as $fisier): ?>
                                        <?php if (in_array($fisier, $melodii, true)): ?>
                                            <div class="rounded-xl border border-[#eee7f6] bg-[#fcfaff] p-4">
                                                <p class="mb-3 break-words font-semibold">
                                                    <?php echo textSigur(numeMelodie($fisier, $metadateMelodii)); ?>
                                                </p>

                                                <audio controls preload="none" class="w-full">
                                                    <source src="audio/<?php echo rawurlencode($fisier); ?>" type="audio/mpeg">
                                                    Browserul tău nu acceptă redarea audio.
                                                </audio>

                                                <div class="mt-3 flex flex-wrap gap-3">
                                                    <a
                                                        href="download.php?fisier=<?php echo rawurlencode($fisier); ?>"
                                                        class="rounded-xl bg-[#eee7f6] px-4 py-2 text-sm font-semibold text-[#68547f] transition hover:bg-[#e3d8f0]"
                                                    >
                                                        Descarcă
                                                    </a>

                                                    <form
                                                        method="POST"
                                                        action="playlisturi.php"
                                                        onsubmit="return confirm('Sigur vrei să elimini această piesă din playlist?');"
                                                    >
                                                        <input type="hidden" name="actiune" value="elimina_piesa">
                                                        <input type="hidden" name="id_playlist" value="<?php echo textSigur($id); ?>">
                                                        <input type="hidden" name="piesa" value="<?php echo textSigur($fisier); ?>">

                                                        <button
                                                            type="submit"
                                                            class="rounded-xl bg-[#f4e8ef] px-4 py-2 text-sm font-semibold text-[#8b526d] transition hover:bg-[#ecd9e5]"
                                                        >
                                                            Elimină din playlist
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="mt-5 text-[#776b84]">
                                    Acest playlist nu conține melodii.
                                </p>
                            <?php endif; ?>

                            <?php
                                $pieseDisponibile = [];

                                foreach ($melodii as $fisier) {
                                    if (!in_array($fisier, $piesePlaylist, true)) {
                                        $pieseDisponibile[] = $fisier;
                                    }
                                }
                            ?>

                            <?php if (count($pieseDisponibile) > 0): ?>
                                <details class="mt-5 rounded-xl border border-[#e5dced] bg-[#fcfaff] p-4">
                                    <summary class="cursor-pointer font-semibold text-[#68547f]">
                                        Adaugă melodii în acest playlist
                                    </summary>

                                    <form method="POST" action="playlisturi.php" class="mt-4">
                                        <input type="hidden" name="actiune" value="adauga">
                                        <input type="hidden" name="id_playlist" value="<?php echo textSigur($id); ?>">

                                        <div class="mb-4 grid gap-3 sm:grid-cols-2">
                                            <?php foreach ($pieseDisponibile as $fisier): ?>
                                                <label class="flex items-start gap-3 rounded-xl border border-[#e8e0f0] bg-white p-3">
                                                    <input
                                                        type="checkbox"
                                                        name="piese[]"
                                                        value="<?php echo textSigur($fisier); ?>"
                                                        class="mt-1 accent-[#806b98]"
                                                    >
                                                    <span class="break-words">
                                                        <?php echo textSigur(numeMelodie($fisier, $metadateMelodii)); ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>

                                        <button
                                            type="submit"
                                            class="rounded-xl bg-[#e9e0f2] px-5 py-3 font-semibold text-[#68547f] transition hover:bg-[#ded0ec]"
                                        >
                                            Adaugă selecția
                                        </button>
                                    </form>
                                </details>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
