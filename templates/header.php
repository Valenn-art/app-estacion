<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'App Estación' ?></title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>css/styles.css?v=<?= time() ?>">
    
    <?php
    if (isset($css)) {
        echo '<link rel="stylesheet" href="' . BASE_URL . 'css/' . $css . '.css?v=' . time() . '">';
    }
    ?>
</head>
<body>
    <nav>
        <div class="container">
            <a href="<?= BASE_URL ?>" class="logo">
                <img src="<?= BASE_URL ?>assets/icons/clima.png" alt="Clima" class="icon-header"> Estaciones Meteorológicas
            </a>
        </div>
    </nav>
    
    <main>