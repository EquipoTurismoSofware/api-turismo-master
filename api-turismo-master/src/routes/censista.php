<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get("/censista/all", function (Request $request, Response $response, array $args) {
    $respuesta = dbGet("SELECT * FROM censista");
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/censista/{id:[0-9]+}/onlyImage", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT foto FROM censista";
    $xSQL .= " WHERE id = " .$args["id"];
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/censista/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM censista";
    $xSQL .= " WHERE id = " .$args["id"];
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/censista/new", function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    //Imagenes
    $directory = $this->get("upload_directory_censistas");
    $tamanio_maximo = $this->get("max_file_size");
    $formatos_permitidos = $this->get("allow_file_format");
    $uploadedFiles = $request->getUploadedFiles();
    //img-uno
    $img_uno = "default.png";
    if (isset($uploadedFiles["img-uno"])) {
        $uploadedFile = $uploadedFiles["img-uno"];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            if ($uploadedFile->getSize() <= $tamanio_maximo) {
                if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                    $img_uno = moveUploadedFile($directory, $uploadedFile, 0, 0);
                }
            }
        }
    }

    $parsedBody["foto"] = $img_uno;
    $respuesta = dbPostWithData("censista", $parsedBody);
    $respuesta->foto = $img_uno;
    return $response
        ->withStatus(201) //Created
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/censista/del/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("censista", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/censista/update/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    $directory = $this->get("upload_directory_censistas");
    $tamanio_maximo = $this->get("max_file_size");
    $formatos_permitidos = $this->get("allow_file_format");
    $uploadedFiles = $request->getUploadedFiles();
    //img-uno
    $img_uno = $parsedBody["foto"];
    if (isset($uploadedFiles["img-uno"])) {
        $uploadedFile = $uploadedFiles["img-uno"];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            if ($uploadedFile->getSize() <= $tamanio_maximo) {
                if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                    $img_uno = moveUploadedFile($directory, $uploadedFile, 0, 0);
                    if ($img_uno == true) {
                        //Eliminar la vieja imagen uno si no es default.jpg
                        $eliminar = $parsedBody["foto"];
                        if ($eliminar != "default.png") {
                            @unlink($this->get("upload_directory_censistas") . "\\$eliminar");
                        }
                    }
                }
            }
        }
    }
    $parsedBody["foto"] = $img_uno;
    unset($parsedBody["id"]);
    $respuesta = dbPatchWithData("censista", $args["id"], $parsedBody);
    $respuesta->foto = $img_uno;
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});

?>