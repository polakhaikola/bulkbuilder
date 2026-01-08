<?php
require_once '../app/Config/config.php';

// Autoload Core Libraries
spl_autoload_register(function ($className) {
    // Namespace implementation or simple require?
    // Going with simple require for Core for now to match the "Simple MVC" style.
    // In a real strict PSR-4 app we would map namespaces.
    // Let's check if file exists in Core, Controllers, Models.

    // Check Core
    if (file_exists('../app/Core/' . $className . '.php')) {
        require_once '../app/Core/' . $className . '.php';
    }
    // Check Controllers
    else if (file_exists('../app/Controllers/' . $className . '.php')) {
        require_once '../app/Controllers/' . $className . '.php';
    }
    // Check Models
    else if (file_exists('../app/Models/' . $className . '.php')) {
        require_once '../app/Models/' . $className . '.php';
    }
});

// Init Core Library (Web Router/App)
$init = new Router();
