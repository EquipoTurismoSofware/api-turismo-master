<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//** Trae todas las imagenes **/

$app->get("/carruseles", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM carrusel_home";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/carrusel/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM carrusel_home WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

// filtra galerias por temporada 

$app->get("/carrusel/galeria/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM carrusel_home WHERE idGHome = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

// trae todas las galerias 
$app->get("/galeriaHome", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM galeria_home ";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Agregar una imagen
$app->post("/addimgcarrusel", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "activo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "activo"
        ),
        "idGHome" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de galeria"
        ),
        "horizontal" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "horizontal"
        ),
        "vertical" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "vertical"
        ),
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody())) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        $directory = $this->get("upload_directory_carrusel");
        $tamanio_maximo = $this->get("max_file_size");
        $formatos_permitidos = $this->get("allow_file_format");
        $uploadedFiles = $request->getUploadedFiles();
        //img-uno
        $img_uno = "default.jpg";
        if (isset($uploadedFiles["img_uno"])) {
            // handle single input with single file upload
            $uploadedFile = $uploadedFiles["img_uno"];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                if ($uploadedFile->getSize() <= $tamanio_maximo) {
                    if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                        $img_uno = moveUploadedFile($directory, $uploadedFile, 0, 0);
                    }
                }
            }
        }
        $parsedBody["image"] = $img_uno;
        $respuesta = dbPostWithData("carrusel_home", $parsedBody);
        $respuesta->foto_uno = $img_uno;
        return $response
            ->withStatus(201) //Created
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
});

/** Actualiza**/

$app->post("/upimgcarrusel/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "activo" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "activo"
        ),
        "idGHome" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "Identificador de galeria"
        ),
        "horizontal" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "horizontal"
        ),
        "vertical" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "vertical"
        ),
        "image" => array(
            "numeric" => true,
            "mayorcero" => 0,
            "tag" => "imagen"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        //Eliminar de $parsedBody id
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("carrusel_home", $args["id"], $parsedBody);
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


/* -----Elimina una imagen ----*/

$app->delete("/delimagen/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $archivo = dbGet("SELECT image FROM carrusel_home WHERE id = " . $args["id"]);
    if ($archivo->err == false && $archivo->data["count"] > 0) {
        $fileX = $archivo->data["registros"][0]->imagen;
        @unlink($this->get("upload_directory") . "\\$fileX");
    }
    $respuesta = dbDelete("carrusel_home", $args["id"]);
    return $response
        ->withStatus(200)
        //->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
