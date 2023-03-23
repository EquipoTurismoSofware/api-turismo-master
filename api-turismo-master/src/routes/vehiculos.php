<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Lista todos los tipos de vehiculos
$app->get("/gettipovehiculos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT tipo_vehiculo.* FROM tipo_vehiculo";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Lista todoas las vehiculos  
$app->get("/getvehiculos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT vehiculos.*, ciudades.nombre AS ciudad FROM vehiculos";
    $xSQL .= " INNER JOIN ciudades ON vehiculos.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY vehiculos.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
// Agregar vehiculo
$app->post("/addvehiculo", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "tipovehiculo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "nombre" => array(
            "max" => 150,
            "tag" => "nombre"
        ),
        "domicilio" => array(
            "max" => 150,
            "tag" => "domicilio"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "email" => array(
            "max" => 150,
            "tag" => "mail"
        ), 
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ), 
        "latitud" => array(
            "max" => 150,
            "tag" => "latitud"
        ),
        "longitud" => array(
            "max" => 150,
            "tag" => "longitud"
        )

    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        $respuesta = dbPostWithData("vehiculos", $parsedBody);
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
//Guardar los cambios de una un tirolesa 
$app->post("/updavehiculo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "tipovehiculo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "nombre" => array(
            "max" => 150,
            "tag" => "nombre"
        ),
        "domicilio" => array(
            "max" => 150,
            "tag" => "domicilio"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "email" => array(
            "max" => 150,
            "tag" => "mail"
        ), 
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ), 
        "latitud" => array(
            "max" => 150,
            "tag" => "latitud"
        ),
        "longitud" => array(
            "max" => 150,
            "tag" => "longitud"
        )

    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        //Eliminar de $parsedBody id
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("vehiculos", $args["id"], $parsedBody);
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
// Elimina un Vehiculo 
$app->delete("/delvehiculo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("vehiculos", $args["id"]);
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
?>