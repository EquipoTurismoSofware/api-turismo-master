<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


   //Todas las Oficinas Turísticas
   $app->get("/getcajeros", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM cajeros";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});



?>