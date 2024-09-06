<?php
// Funzione per calcolare la distanza in anni luce
function calcolaAnniLuce($distanzaKm)
{
    $annoLuceKm = 9.461e12; // 1 anno luce in chilometri
    return $distanzaKm / $annoLuceKm;
}

// Funzione per calcolare la distanza in chilometri
function calcolaChilometri($distanzaAnniLuce)
{
    $annoLuceKm = 9.461e12; // 1 anno luce in chilometri
    return $distanzaAnniLuce * $annoLuceKm;
}

$errore = "";
$distanza = "";
$risultato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["tipo_calcolo"])) {
        $tipoCalcolo = $_POST["tipo_calcolo"];

        if ($tipoCalcolo == "anni_luce") {
            if (isset($_POST["distanza_km"]) && is_numeric($_POST["distanza_km"]) && $_POST["distanza_km"] >= 0) {
                $distanza = $_POST["distanza_km"];
                $risultato = calcolaAnniLuce($distanza);
            } else {
                $errore = "Inserisci una distanza valida in chilometri.";
            }
        } elseif ($tipoCalcolo == "chilometri") {
            if (isset($_POST["distanza_anni_luce"]) && is_numeric($_POST["distanza_anni_luce"]) && $_POST["distanza_anni_luce"] >= 0) {
                $distanza = $_POST["distanza_anni_luce"];
                $risultato = calcolaChilometri($distanza);
            } else {
                $errore = "Inserisci una distanza valida in anni luce.";
            }
        } else {
            $errore = "Seleziona un tipo di calcolo valido.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolo Distanza Astronomica</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }

        #background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            /* Per mettere lo sfondo dietro il contenuto */
        }

        .container {
            position: relative;
            z-index: 1;
            /* Per mettere il contenuto sopra lo sfondo */
        }
    </style>
</head>

<body>
    <div id="background"></div>
    <div class="container mt-4">
        <h1 class="text-center">Calcolatore di Distanza Astronomica</h1>

        <form method="post" class="mt-4">
            <div class="form-group">
                <label for="tipo_calcolo">Seleziona tipo di calcolo:</label>
                <select class="form-control" id="tipo_calcolo" name="tipo_calcolo" required>
                    <option value="">Scegli...</option>
                    <option value="anni_luce">Calcola anni luce da chilometri</option>
                    <option value="chilometri">Calcola chilometri da anni luce</option>
                </select>
            </div>

            <div class="form-group" id="inputDistanzaKm" style="display: none;">
                <label for="distanza_km">Distanza in chilometri:</label>
                <input type="number" class="form-control" id="distanza_km" name="distanza_km" step="any" value="<?php echo htmlspecialchars($distanza); ?>">
            </div>

            <div class="form-group" id="inputDistanzaAnniLuce" style="display: none;">
                <label for="distanza_anni_luce">Distanza in anni luce:</label>
                <input type="number" class="form-control" id="distanza_anni_luce" name="distanza_anni_luce" step="any" value="<?php echo htmlspecialchars($distanza); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Calcola</button>
        </form>

        <?php if (!empty($errore)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo htmlspecialchars($errore); ?>
            </div>
        <?php elseif (!empty($risultato)): ?>
            <div class="alert alert-success mt-3">
                <?php if (isset($_POST["tipo_calcolo"]) && $_POST["tipo_calcolo"] == "anni_luce"): ?>
                    La distanza di <?php echo htmlspecialchars($distanza); ?> chilometri corrisponde a circa <?php echo number_format($risultato, 12); ?> anni luce.
                <?php elseif (isset($_POST["tipo_calcolo"]) && $_POST["tipo_calcolo"] == "chilometri"): ?>
                    La distanza di <?php echo htmlspecialchars($distanza); ?> anni luce corrisponde a circa <?php echo number_format($risultato, 0); ?> chilometri.
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Three.js Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inizializzazione della scena, fotocamera e renderer
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer();
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.getElementById('background').appendChild(renderer.domElement);

            // Crea una geometria sferica per la sfera
            const geometry = new THREE.SphereGeometry(5, 32, 32);
            const texture = new THREE.TextureLoader().load('https://threejs.org/examples/textures/planets/earth_atmos_2048.jpg');
            const material = new THREE.MeshBasicMaterial({
                map: texture
            });
            const sphere = new THREE.Mesh(geometry, material);

            // Aggiungi la sfera alla scena
            scene.add(sphere);

            // Posiziona la fotocamera
            camera.position.z = 15;

            // Funzione di animazione
            const animate = () => {
                requestAnimationFrame(animate);

                // Ruota la sfera
                sphere.rotation.y += 0.01;

                // Renderizza la scena
                renderer.render(scene, camera);
            };

            // Avvia l'animazione
            animate();

            // Gestisci il ridimensionamento della finestra
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });

            // Gestione del cambiamento del tipo di calcolo
            document.getElementById('tipo_calcolo').addEventListener('change', function() {
                var tipo = this.value;
                if (tipo === 'anni_luce') {
                    document.getElementById('inputDistanzaKm').style.display = 'block';
                    document.getElementById('inputDistanzaAnniLuce').style.display = 'none';
                } else if (tipo === 'chilometri') {
                    document.getElementById('inputDistanzaKm').style.display = 'none';
                    document.getElementById('inputDistanzaAnniLuce').style.display = 'block';
                } else {
                    document.getElementById('inputDistanzaKm').style.display = 'none';
                    document.getElementById('inputDistanzaAnniLuce').style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>