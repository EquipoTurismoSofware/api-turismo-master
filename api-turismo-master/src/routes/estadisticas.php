<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//El ultimo reporte
$app->get("/reporte/ultimo", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM reporte ORDER BY id DESC LIMIT 1";
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Graficos de un reporte en particular
$app->get("/graficos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT id FROM reporte ORDER BY id DESC LIMIT 1";
    $respuesta1 = dbGet($xSQL);

    $xSQL = "SELECT grafico.id, reporte.nombre AS titulo, reporte.fechaDesde, reporte.fechaHasta, tipo_grafico.nombre AS tipoNombre, tipo_grafico.tipo AS tipoGrafico FROM grafico";
    $xSQL .= " INNER JOIN reporte ON grafico.idReporte = reporte.id";
    $xSQL .= " INNER JOIN tipo_grafico ON grafico.idtipo = tipo_grafico.id";
    $xSQL .= " WHERE grafico.idReporte = " .$respuesta1->data["registros"][0]->id;
    $respuesta = dbGet($xSQL);

    for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
        $xSQL = "SELECT etiqueta, valor FROM valor_grafico";
        $xSQL .= " WHERE idGrafico = " .$respuesta->data["registros"][$i]->id;
        $valores = dbGet($xSQL);

        $respuesta->data["registros"][$i]->valores = $valores->data["registros"];
    }

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Me trae todos los graficos
$app->get("/grafico/all", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT id FROM reporte ORDER BY id DESC LIMIT 1";
    $respuesta1 = dbGet($xSQL);

    $xSQL = "SELECT grafico.id, tipo_grafico.tipo AS tipoGrafico FROM grafico";
    $xSQL .= " INNER JOIN reporte ON grafico.idReporte = reporte.id";
    $xSQL .= " INNER JOIN tipo_grafico ON grafico.idtipo = tipo_grafico.id";
    $xSQL .= " WHERE grafico.idReporte = " .$respuesta1->data["registros"][0]->id;
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Me trae todos los tipos de graficos
$app->get("/tiposgraficos/all", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM tipo_grafico";
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Graficos de un reporte en particular
$app->post("/addreporte", function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    $respuesta = dbPostWithData("reporte", $parsedBody);

    return $response
        ->withStatus(201) //Created
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/addvalores", function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    $respuesta = dbPostWithData("valor_grafico", $parsedBody);
    
    return $response
        ->withStatus(201) //Created
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/grafico/addNew", function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    $respuesta = dbPostWithData("grafico", $parsedBody);

    return $response
        ->withStatus(201) //Created
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
});
?>