<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Arboles

//[GET]

//Obtener las últimas fotos cargadas
$app->get("/galeria_localidades", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gl.id, gl.imagen, d.nombre, d.toplocalidad, t.nombre
    FROM galeria_localidades gl 
    JOIN departamentos d
    JOIN gal_tag gt
    JOIN tag t
    WHERE gl.idloc = d.id
    AND gl.id = gt.id_img
    AND gt.id_tag = t.id";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

echo($respuesta);