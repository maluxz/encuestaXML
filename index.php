<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $opciones = simplexml_load_file('opciones.xml');
    $ip = $_SERVER['REMOTE_ADDR'];

    if (isset($_POST['opcion'])) {
        $opcion = $opciones->addChild('opcion');
        $opcion->addChild('nombre', $_POST['opcion']);
        $opcion->addChild('votos', 0);
        $opcion->addChild('ips', '');
    }

    if (isset($_POST['voto'])) {
        foreach ($_POST['voto'] as $voto) {
            foreach ($opciones->opcion as $opcion) {
                if ($opcion->nombre == $voto) {
                    $ips = explode(',', $opcion->ips);
                    if (!in_array($ip, $ips)) {
                        $votos = intval($opcion->votos);
                        $votos++;
                        $opcion->votos = $votos;
                        $opcion->ips .= $ip . ',';
                    }
                }
            }
        }
    }
    $opciones->asXML('opciones.xml');
}

$opciones = simplexml_load_file('opciones.xml');
$totalVotos = 0;

foreach ($opciones->opcion as $opcion) {
    $totalVotos += intval($opcion->votos);
}

foreach ($opciones->opcion as $opcion) {
    $opcion->porcentaje = ($totalVotos > 0) ? (intval($opcion->votos) / $totalVotos) * 100 : 0;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Encuesta</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }

        h1 {
            font-size: 2em;
            color: #444;
            text-align: center;
        }

        form {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 1.2em;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 20px;
            font-size: 1em;
        }

        input[type="submit"] {
            background-color: #009688;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 1em;
        }

        input[type="submit"]:hover {
            background-color: #00796b;
        }

        .barra {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            height: 30px;
            margin-bottom: 10px;
        }

        .progreso {
            background-color: #009688;
            color: #fff;
            height: 100%;
            line-height: 30px;
            padding-left: 10px;
        }
    </style>
</head>

<body>
    <h1>Encuesta</h1>

    <form method="POST">
        <label for="opcion">Añadir opción:</label>
        <input type="text" id="opcion" name="opcion" required>
        <input type="submit" value="Añadir">
    </form>

    <?php if ($opciones->opcion): ?>
        <form method="POST">
            <?php foreach ($opciones->opcion as $opcion): ?>
                <input type="checkbox" id="<?php echo $opcion->nombre; ?>" name="voto[]" value="<?php echo $opcion->nombre; ?>">
                <label for="<?php echo $opcion->nombre; ?>">
                    <?php echo $opcion->nombre; ?> (
                    <?php echo $opcion->votos; ?> votos)
                </label><br>
                <div class="barra">
                    <div class="progreso" style="width: <?php echo $opcion->porcentaje; ?>%">
                        <?php echo $opcion->votos; ?> votos
                    </div>
                </div>
            <?php endforeach; ?>
            <input type="submit" value="Votar">
        </form>
    <?php endif; ?>
</body>

</html>