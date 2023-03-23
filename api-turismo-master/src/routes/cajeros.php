<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//Lista todos los bancos 
$app->get("/getbancos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT bancos.* FROM bancos";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Lista todos los cajeros
$app->get("/getcajeros", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT cajeros.*, ciudades.nombre AS ciudad FROM cajeros";
    $xSQL .= " INNER JOIN ciudades ON cajeros.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY cajeros.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
/* Muestras los datos de una cajero determinada */
// $app->get("/cajero/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
//     $xSQL = "SELECT * FROM cajeros WHERE id = " . $args["id"];
//     $respuesta = dbGet($xSQL);
//     return $response
//         ->withStatus(200)
//         ->withHeader("Content-Type", "application/json")
//         ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
// });
// //Lista cajeros por localidad
// $app->get("/cajeros/localidad", function (Request $request, Response $response, array $args) {
//     $xSQL = " SELECT bancos.nombre as nombre, cajeros.*  FROM cajeros, bancos ";
//     $xSQL .= " WHERE cajeros.tpo_bco = bancos.id";
//     $xSQL .= " AND  cajeros.idlocalidad = " . $args["id"];
//     $respuesta = dbGet($xSQL);
//     return $response
//         ->withStatus(200)
//         ->withHeader("Content-Type", "application/json")
//         ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
// });

/* Muestras los datos de una cajero determinada */
$app->get("/cajero/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM cajeros WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Agrega un cajero 
$app->post("/addcajero", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "tpo_bco" => array(
            "max" => 150,
            "tag" => "Nombre del Banco"
        ),
        "domicilio" => array(
            "max" => 150,
            "tag" => "domicilio"
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
        $respuesta = dbPostWithData("cajeros", $parsedBody);
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
//Guardar los cambios de una un cajero
$app->post("/updatecajero/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de Ciudad"
        ),
        "tpo_bco" => array(
            "max" => 150,
            "tag" => "Nombre del Banco"
        ),
        "domicilio" => array(
            "max" => 150,
            "tag" => "domicilio"
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
        $respuesta = dbPatchWithData("cajeros", $args["id"], $parsedBody);
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
// Elimina un cajero
$app->delete("/delcajero/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("cajeros", $args["id"]);
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
?>