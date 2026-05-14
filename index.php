<?php
// 1. KORAK: Začetek seje
session_start();

// Pomožna funkcija za kocke
function narisi_kocko($stevilka) {
    $kocke = [
        1 => '&#9856;', 2 => '&#9857;', 3 => '&#9858;', 
        4 => '&#9859;', 5 => '&#9860;', 6 => '&#9861;'
    ];
    return "<span class='kocka-ikona'>{$kocke[$stevilka]}</span>";
}

// 2. KORAK: Obdelava podatkov
if (isset($_POST['gumb_igraj'])) {
    $igralci = [];
    for ($i = 1; $i <= 3; $i++) {
        $k1 = rand(1, 6);
        $k2 = rand(1, 6);
        $k3 = rand(1, 6);
        $igralci[] = [
            'ime' => htmlspecialchars($_POST["ime$i"]),
            'priimek' => htmlspecialchars($_POST["priimek$i"]),
            'naslov' => htmlspecialchars($_POST["naslov$i"]),
            'met' => [$k1, $k2, $k3],
            'vsota' => ($k1 + $k2 + $k3)
        ];
    }
    $_SESSION['igra_podatki'] = $igralci;
    header("Location: index.php?rezultati=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kocke Royale</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --accent: #38bdf8;
            --winner-color: #facc15;
            --text-color: #f1f5f9;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-color);
            text-align: center; 
            margin: 0; padding: 20px;
        }

        .container { max-width: 900px; margin: auto; }

        h1 { color: var(--accent); text-transform: uppercase; letter-spacing: 2px; }

        /* Kartice igralcev */
        .grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 30px; }
        
        .igralec-card { 
            background: var(--card-bg); 
            padding: 20px; 
            border-radius: 15px; 
            width: 250px; 
            transition: transform 0.3s;
            border: 2px solid transparent;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .zmagovalec { 
            border-color: var(--winner-color); 
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.4);
            transform: scale(1.05);
        }

        .kocka-ikona { font-size: 60px; color: var(--accent); display: inline-block; animation: roll 0.5s ease-out; }
        
        @keyframes roll {
            0% { transform: rotate(-45deg) scale(0.5); opacity: 0; }
            100% { transform: rotate(0) scale(1); opacity: 1; }
        }

        /* Obrazec */
        input { 
            width: 80%; padding: 10px; margin: 10px 0; 
            border-radius: 5px; border: none; background: #334155; color: white;
        }

        button { 
            background: var(--accent); color: var(--bg-color); 
            border: none; padding: 15px 40px; border-radius: 30px; 
            font-weight: bold; cursor: pointer; transition: 0.3s;
        }

        button:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(56, 189, 248, 0.4); }

        .timer-box { margin-top: 40px; font-size: 1.2rem; opacity: 0.8; }
        #seconds { font-weight: bold; color: var(--accent); font-size: 1.5rem; }
    </style>
</head>
<body>

<div class="container">

<?php if (!isset($_GET['rezultati'])): ?>
    <h1>🎲 Kocke Royale</h1>
    <p>Vnesite podatke igralcev in preizkusite srečo!</p>
    
    <form method="POST">
        <div class="grid">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="igralec-card">
                    <h3>Igralec <?php echo $i; ?></h3>
                    <input type="text" name="ime<?php echo $i; ?>" placeholder="Ime" required>
                    <input type="text" name="priimek<?php echo $i; ?>" placeholder="Priimek" required>
                    <input type="text" name="naslov<?php echo $i; ?>" placeholder="Naslov" required>
                </div>
            <?php endfor; ?>
        </div>
        <br><br>
        <button type="submit" name="gumb_igraj">VRŽI KOCKE!</button>
    </form>

<?php else: ?>
    <h1>🏆 Rezultati Meta</h1>
    
    <div class="grid">
        <?php
        $podatki = $_SESSION['igra_podatki'] ?? [];
        $naj_vsota = 0;
        foreach ($podatki as $p) { if ($p['vsota'] > $naj_vsota) $naj_vsota = $p['vsota']; }

        $vsi_zmagovalci = [];

        foreach ($podatki as $igralec) {
            $is_winner = ($igralec['vsota'] == $naj_vsota);
            if ($is_winner) $vsi_zmagovalci[] = $igralec['ime'] . " " . $igralec['priimek'];
            
            $class = $is_winner ? "zmagovalec" : "";
            ?>
            <div class="igralec-card <?php echo $class; ?>">
                <h3><?php echo $igralec['ime'] . " " . $igralec['priimek']; ?></h3>
                <p style="font-size: 0.8rem; opacity: 0.7;"><?php echo $igralec['naslov']; ?></p>
                <div style="margin: 15px 0;">
                    <?php foreach ($igralec['met'] as $st) echo narisi_kocko($st); ?>
                </div>
                <div style="font-size: 1.2rem;">Vsota: <strong><?php echo $igralec['vsota']; ?></strong></div>
                <?php if($is_winner) echo "<div style='color:var(--winner-color); margin-top:5px;'>★ ZMAGOVALEC ★</div>"; ?>
            </div>
        <?php } ?>
    </div>

    <div class="timer-box">
        <h2>Zmagovalec: <span style="color: var(--winner-color);"><?php echo implode(", ", $vsi_zmagovalci); ?></span></h2>
        Preusmeritev na začetek čez <span id="seconds">10</span> sekund...
    </div>

    <script>
        let timeLeft = 10;
        const timerDisplay = document.getElementById('seconds');
        
        const countdown = setInterval(() => {
            timeLeft--;
            timerDisplay.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.href = "index.php";
            }
        }, 1000);
    </script>

<?php endif; ?>

</div>
</body>
</html>
