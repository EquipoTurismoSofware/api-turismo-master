<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Obtener todas las Agencias de viajes 
$app->get("/casascambio", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT casas_cambio.*, ciudades.nombre AS ciudad FROM casas_cambio";
    $xSQL .= " INNER JOIN ciudades ON casas_cambio.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY casas_cambio.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/casascambio/ciudades", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT DISTINCT ciudades.foto, ciudades.nombre AS ciudad, ciudades.id, zonas_ciudades.idzona AS ZonaId FROM casas_cambio";
    $xSQL .= " INNER JOIN ciudades ON casas_cambio.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN zonas_ciudades ON ciudades.id= zonas_ciudades.idciudad";
    $xSQL .= " ORDER BY casas_cambio.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Guías de Turismo

$app->post("/addcasacambio", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "nombre" => array(
            "max" => 150,
            "tag" => "nombre"
        ),
        "direccion" => array(
            "max" => 150,
            "tag" => "domicilio"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ),
        
        "horarioCierre" => array(
            "max" => 150,
            "tag" => "horarioCierre"
        ),
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        $respuesta = dbPostWithData("casas_cambio", $parsedBody);
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

/* Muestras los datos de una agencia determinada */
$app->get("/casacambio/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM casas_cambio WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Guardar los cambios de una un guia
$app->post("/updatecasacambio/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "nombre" => array(
            "max" => 150,
            "tag" => "nombre"
        ),
        "direccion" => array(
            "max" => 150,
            "tag" => "domicilio"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ),
        
        "horarioCierre" => array(
            "max" => 150,
            "tag" => "horarioCierre"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        //Eliminar de $parsedBody id
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("casas_cambio", $args["id"], $parsedBody);
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

$app->delete("/casacambio/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("casas_cambio", $args["id"]);
   
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

?>