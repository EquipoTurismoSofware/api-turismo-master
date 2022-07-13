<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Arboles

//[GET]

//Obtener las últimas fotos cargadas
$app->get("/galeria_localidades", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gl.id id, gl.imagen img, d.nombre localidad, c.nombre ciudad, d.toplocalidad toplocalidad, t.nombre tag
    FROM galeria_localidades gl 
    JOIN departamentos d
    JOIN gal_tag gt
    JOIN tag t
    JOiN ciudades c
    WHERE gl.idloc = d.id
    AND gl.id = gt.id_img
    AND gt.id_tag = t.id
    AND gl.idciudad = c.id
    ORDER BY d.nombre, d.toplocalidad DESC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});