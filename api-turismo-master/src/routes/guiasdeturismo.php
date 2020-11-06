<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Guías de Turismo

$app->post("/guiasturismox", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idciudad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "legajo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de legajo"
        ),
        "categoria" => array(
            "min" => 1,
            "max" => 150,
            "tag" => "categoria"
        ),
        "nombre" => array(
            "max" => 150,
            "tag" => "nombre"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "ambito" => array(
            "max" => 150,
            "tag" => "ambito"
        ),
        "correo" => array(
            "max" => 150,
            "tag" => "correo"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        $respuesta = dbPostWithData("guias_turismo", $parsedBody);
        return $response
            ->withStatus(201) //Created
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
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


//Datos de todos los guias
$app->get("/guiasturismo", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT guias_turismo.id, guias_turismo.nombre, categoria, legajo, ambito, telefono, correo, ciudades.nombre as ciudad FROM guias_turismo";
    $xSQL .= " INNER JOIN ciudades ON ciudades.id = guias_turismo.idciudad";
    $xSQL .= " ORDER BY ciudades.nombre";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener los datos de una determinado guia
$app->get("/guiasturismo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM guias_turismo WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


//Guardar los cambios de una un guia
$app->post("/guiasturismo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idciudad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "legajo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de legajo"
        ),
        "categoria" => array(
            "min" => 1,
            "max" => 150,
            "tag" => "categoria"
        ),
        "nombre" => array(
            "max" => 150,
            "tag" => "nombre"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "ambito" => array(
            "max" => 150,
            "tag" => "ambito"
        ),
        "correo" => array(
            "max" => 150,
            "tag" => "correo"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        //Eliminar de $parsedBody id
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("guias_turismo", $args["id"], $parsedBody);
        if ($respuesta->err) {
            return $response
                ->withStatus(409) //Conflicto
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        } else {
            return $response
                ->withStatus(200) //Ok
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
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



//Eliminar un guia Turistico
$app->delete("/guiasturismo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("guias_turismo", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Agregar un Guía 
