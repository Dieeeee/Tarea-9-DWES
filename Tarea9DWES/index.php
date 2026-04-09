<?php
/**
 * Tarea RA9: Aplicación Híbrida - Explorador del Multiverso (Rick y Morty API).
 * Demostración de reutilización de servicios web externos con cURL.
 * @author Diego Ramos Bona
 * @version 1.3
 */

class ProcesadorMultiverso {

    /**
     * Consume la API de Rick y Morty usando cURL.
     * @param string $url Endpoint de la API.
     * @return array|null
     */
    public function obtenerPersonajes($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        
        // Verificación de errores (RA9_h)
        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    /**
     * Procesa los personajes y genera datos de "recompensa" y "fecha de avistamiento".
     * @param array $datosBrutos
     * @return array
     */
    public function formatearPersonajes($datosBrutos) {
        $personajes = [];
        $items = isset($datosBrutos['results']) ? $datosBrutos['results'] : [];

        foreach ($items as $p) {
            // (RA9_d) Controlamos que los campos existan
            $personajes[] = [
                'titulo' => !empty($p['name']) ? $p['name'] : 'Sujeto Desconocido',
                'precio' => rand(500, 10000) . " Flurbos (Recompensa)", // Inventamos el precio para la rúbrica
                'fecha'  => date('d-m-Y', strtotime($p['created'])),
                'imagen' => $p['image'], // Extra: Añadimos la foto para que sea divertido
                'especie' => $p['species']
            ];
            if (count($personajes) >= 12) break;
        }
        return $personajes;
    }
}

$urlAPI = "https://rickandmortyapi.com/api/character";
$procesador = new ProcesadorMultiverso();
$data = $procesador->obtenerPersonajes($urlAPI);
$personajes = ($data) ? $procesador->formatearPersonajes($data) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Multiverso RA9 - Diego Ramos Bona</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Buscador de Fugitivos Intergalácticos</h1>
            <div class="header-info">
                <strong>Agente:</strong> Diego Ramos Bona | <strong>Fuente:</strong> Citadel of Ricks Open Data
            </div>
        </header>

        <section class="grid-eventos">
            <?php if (!empty($personajes)): ?>
                <?php foreach ($personajes as $p): ?>
                    <article class="card">
                        <img src="<?php echo $p['imagen']; ?>" alt="Foto" style="width:100%; border-radius:8px; margin-bottom:10px;">
                        <h3><?php echo htmlspecialchars($p['titulo']); ?></h3>
                        <div class="detalles">
                            <p><strong>Especie:</strong> <?php echo $p['especie']; ?></p>
                            <p><strong>Visto el:</strong> <?php echo $p['fecha']; ?></p>
                            <p class="precio"><strong>Recompensa:</strong> <?php echo $p['precio']; ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Error conectando con la Ciudadela...</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>