<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//Lista todas las terminales  
$app->get("/getterminales", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT terminales.*, ciudades.nombre AS ciudad FROM terminales";
    $xSQL .= " INNER JOIN ciudades ON terminales.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY terminales.idlocalidad";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

/* Muestras los datos de una tirolesa determinada */
$app->get("/terminal/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM terminales WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


// Agregar terminales 

$app->post("/addterminal", function (Request $request, Response $response, array $args) {
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
        "domicilio" => array(
            "max" => 150,
            "tag" => "domicilio"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "interno" => array(
            "max" => 150,
            "tag" => "interno"
        ),
        "email" => array(
            "max" => 150,
            "tag" => "email"
        ),
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ),
        "responsable" => array(
            "max" => 150,
            "tag" => "responsable"
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
        $respuesta = dbPostWithData("terminales", $parsedBody);
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
$app->post("/updaterminal/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
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
        "domicilio" => array(
            "max" => 150,
            "tag" => "domicilio"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "telefono"
        ),
        "interno" => array(
            "max" => 150,
            "tag" => "interno"
        ),
        "email" => array(
            "max" => 150,
            "tag" => "email"
        ),
        "web" => array(
            "max" => 150,
            "tag" => "web"
        ),
        "responsable" => array(
            "max" => 150,
            "tag" => "responsable"
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
        $respuesta = dbPatchWithData("terminales", $args["id"], $parsedBody);
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


$app->delete("/delterminal/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("terminales", $args["id"]);
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
?>