<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



// Obtiene todas las etiqutas 

$app->get("/tags", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT id, nombre, true as visible FROM tag ORDER BY nombre ASC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Agregar un nuevo tag
$app->post("/addtag", function (Request $request, Response $response, array $args){
    $reglas = array(
        "nombre" => array(
            "tag" => "Nombre"
        )
    );
    $validar = new Validate();
    $parsedBody = $request->getParsedBody();
    if($validar->validar($parsedBody, $reglas)){
        $data = array(
            "nombre" => $parsedBody["nombre"]
        );
        $respuesta = dbPostWithData("tag",$data);
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type","application/json")
            ->write(json_encode($respuesta,
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    } else {
        $resperr = new stdClass();
        $resperr->err = true;
        $resperr->errMsg = "Hay errores en los datos suministrados";
        $resperr->errMsgs = $validar->errors();
        return $response
        ->withStatus(409) //Conflicto
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
});
?>