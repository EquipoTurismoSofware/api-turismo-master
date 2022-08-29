<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Obtener todas las Agencias de viajes 
$app->get("/agencias/viaje", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT agencias_viaje.*, ciudades.nombre AS ciudad FROM agencias_viaje";
    $xSQL .= " INNER JOIN ciudades ON agencias_viaje.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY agencias_viaje.idlocalidad, agencias_viaje.nombre";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/agencias/ciudades", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT DISTINCT ciudades.foto, ciudades.nombre AS ciudad, ciudades.id, zonas_ciudades.idzona AS ZonaId FROM agencias_viaje";
    $xSQL .= " INNER JOIN ciudades ON agencias_viaje.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN zonas_ciudades ON ciudades.id= zonas_ciudades.idciudad";
   //$xSQL .= " WHERE agencias_viaje.adhiereCovid > 0";
    $xSQL .= " ORDER BY ciudades.id";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/agencias/adhiereCovid", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT agencias_viaje.*, ciudades.nombre AS ciudad FROM agencias_viaje";
    $xSQL .= " INNER JOIN ciudades ON agencias_viaje.idlocalidad = ciudades.id";
    $xSQL .= " WHERE agencias_viaje.adhiereCovid > 0";
    $xSQL .= " ORDER BY agencias_viaje.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/agencias/adhiereDosep", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT agencias_viaje.*, ciudades.nombre AS ciudad FROM agencias_viaje";
    $xSQL .= " INNER JOIN ciudades ON agencias_viaje.idlocalidad = ciudades.id";
    $xSQL .= " WHERE agencias_viaje.adhiereDosep > 0";
    $xSQL .= " ORDER BY agencias_viaje.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Guías de Turismo

$app->post("/addagenciadeviajes", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "legajo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de legajo"
        ),
        "registro" => array(
            "min" => 1,
            "max" => 150,
            "tag" => "registro"
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
        "mail" => array(
            "max" => 150,
            "tag" => "mail"
        ),
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ),
        "representante" => array(
            "max" => 150,
            "tag" => "representante"
        ),
        "adhiereDosep" => array(
            "max" => 150,
            "tag" => "adhiereDosep"
        ),
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        $respuesta = dbPostWithData("agencias_viaje", $parsedBody);
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
$app->get("/agenciasviaje/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM agencias_viaje WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Guardar los cambios de una un guia
$app->post("/updagencias/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "legajo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de legajo"
        ),
        "registro" => array(
            "min" => 1,
            "max" => 150,
            "tag" => "registro"
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
        "mail" => array(
            "max" => 150,
            "tag" => "mail"
        ),
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ),
        "representante" => array(
            "max" => 150,
            "tag" => "representante"
        ),
        "adhiereDosep" => array(
            "max" => 150,
            "tag" => "adhiereDosep"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        //Eliminar de $parsedBody id
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("agencias_viaje", $args["id"], $parsedBody);
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

$app->delete("/agencias/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("agencias_viaje", $args["id"]);
   
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

?>