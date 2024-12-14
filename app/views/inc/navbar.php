<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/bulma.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/estilos.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <section id="navbar">
        <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top has-background-success" aria-label="navbar">
            <div class="container-fluid">
                <img src="<?php echo APP_URL; ?>/app/views/img/logo.jpeg" class="img-fluid rounded-circle px-4" alt="Imagen"
                    style="max-height: 50px;">
                <a class="nav-link active text-white col-lg-2 me-0 fs-5" href="<?php echo APP_URL; ?>dashboard/">Creciendo Juntos</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="offcanvas offcanvas-end text-bg-dark" style="width: 270px;" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">¡Bienvenidos!</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav mx-auto text-center">
                            <li class="nav-item">
                                <a class="nav-link active fs-5 px-4" href="<?php echo APP_URL; ?>dashboard/Inicio">Inicio</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fs-5 px-4 text-white" href="#Servicios" role="button" data-bs-toggle="dropdown" aria-expanded="false">Servicios</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item fs-5" href="<?php echo APP_URL; ?>dashboard#Cuid_niños">Cuidado de Niños</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item fs-5" href="<?php echo APP_URL; ?>dashboard#Cuid_niños">Estimulación Temprana</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item fs-5" href="<?php echo APP_URL; ?>dashboard#Cuid_niños">Atención Personalizada</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active fs-5 px-4" aria-current="page" href="<?php echo APP_URL; ?>dashboard/Contactanos">Contáctanos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active fs-5 px-4" aria-current="page" href="<?php echo APP_URL; ?>dashboard/Nosotros">Nosotros</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fs-5 px-4 text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Funciones</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item fs-5" href="<?php echo APP_URL; ?>userNew/">Nuevo</a></li>
                                    <li><a class="dropdown-item fs-5" href="<?php echo APP_URL; ?>userList/">Lista</a></li>
                                    <li><a class="dropdown-item fs-5" href="<?php echo APP_URL; ?>userSearch/">Buscar</a></li>
                                </ul>
                            </li>
                        </ul>
                        <div class="dropdown navbar-end">
                            <a class="nav-link dropdown-toggle fs-5 px-4 text-white d-flex align-items-center text-decoration-none" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <figure class="image is-64x64 mb-0 pt-1" style="max-height: 50px;">
                                    <?php
                                    if (is_file("./app/views/fotos/" . $_SESSION['foto']) && !empty($_SESSION['foto'])) {
                                        echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/' . $_SESSION['foto'] . '" alt="Foto de usuario" style="width: 40px; height: 40px;">';
                                    } else {
                                        echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/default.png" alt="Foto por defecto" style="width: 40px; height: 40px;">';
                                    }
                                    ?>
                                </figure>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="dropdownMenuLink">
                                <li>
                                    <span class="dropdown-item-text fw-bold"><?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?>
                                    </span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <a class="dropdown-item-text  text-decoration-none" href="<?php echo APP_URL . "userUpdate/" . $_SESSION['id'] . "/"; ?>">
                                    Mi cuenta
                                </a>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <a class="dropdown-item-text  text-decoration-none" href="<?php echo APP_URL . "userPhoto/" . $_SESSION['id'] . "/"; ?>">
                                    Mi foto
                                </a>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item-text text-decoration-none text-danger fw-bold" href="<?php echo APP_URL . "logOut/"; ?>" id="btn_exit">Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </section>

</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
