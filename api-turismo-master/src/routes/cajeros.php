<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


   //Todas las Oficinas Turísticas
   $app->get("/getcajeros", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT bancos.nombre as nombre, cajeros.* FROM cajeros, bancos";
    $xSQL .= " WHERE cajeros.tpo_bco = bancos.id";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


    //Todas las Oficinas de una Localidad
    $app->get("/cajeros/localidad/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
        $xSQL = " SELECT bancos.nombre as nombre, cajeros.*  FROM cajeros, bancos ";
        $xSQL .= " WHERE cajeros.tpo_bco = bancos.id";
        $xSQL .= " AND  cajeros.idlocalidad = ". $args["id"];
        $respuesta = dbGet($xSQL);
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

?>