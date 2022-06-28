<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Arboles

//[GET]

//Obtener los últimos arboles cargados
$app->get("/arboles/{cantidad:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM arboles ORDER BY id DESC LIMIT 0, " . $args["cantidad"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener los últimas X Arboles
$app->get("/arboles", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM arboles ORDER BY id DESC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener los datos de un determinado árbol
$app->get("/arbol/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM arboles WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//[POST]

//Agregar un árbol
$app->post("/arbol", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "nombre_popular" => array(
            "min" => 3,
            "max" => 50,
            "tag" => "Nombre popular"
        ),
        "nombre_cientifico" => array(
            "min" => 3,
            "max" => 50,
            "tag" => "Nombre científico"
        ),
        "origen" => array(
            "min" => 3,
            "max" => 75,
            "tag" => "Origen"
        ),
        "descripcion" => array(
            "min" => 3,
            "tag" => "Descripcion"
        ),
        "descripcionHTML" => array(
            "tag" => "descripcionHTML"
        )  
     );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Preguntar si sacar validacion de fecha
        $fecha_valida = true;//false if validation is needed
        // if (strpos($parsedBody["fecha"], "-") !== false) {
        //     $data_fecha = explode("-", $parsedBody["fecha"]);
        //     //YYYY-MM-DD
        //     if (count($data_fecha) == 3) {
        //         $fecha_valida = checkdate($data_fecha[1], $data_fecha[2], $data_fecha[0]);
        //     }
        // }
        if ($fecha_valida == true) {
            //Imágenes
            $directory = $this->get("upload_directory_arboles");
            $tamanio_maximo = $this->get("max_file_size");
            $formatos_permitidos = $this->get("allow_file_format");
            $uploadedFiles = $request->getUploadedFiles();
            //img-uno
            $img_uno = "default.jpg";
            if (isset($uploadedFiles["img-uno"])) {
                // handle single input with single file upload
                $uploadedFile = $uploadedFiles["img-uno"];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile->getSize() <= $tamanio_maximo) {
                        if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                            $img_uno = moveUploadedFile($directory, $uploadedFile, 0, 0);
                        }
                    }
                }
            }
            //img-dos
            $img_dos = "default.jpg";
            if (isset($uploadedFiles["img-dos"])) {
                // handle single input with single file upload
                $uploadedFile = $uploadedFiles["img-dos"];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile->getSize() <= $tamanio_maximo) {
                        if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                            $img_dos = moveUploadedFile($directory, $uploadedFile, 0, 1);
                        }
                    }
                }
            }

             //img-tres
             $img_tres = "default.jpg";
             if (isset($uploadedFiles["img-tres"])) {
                 // handle single input with single file upload
                 $uploadedFile = $uploadedFiles["img-tres"];
                 if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                     if ($uploadedFile->getSize() <= $tamanio_maximo) {
                         if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                             $img_tres = moveUploadedFile($directory, $uploadedFile, 0, 2);
                         }
                     }
                 }
             }
            //img-cuatro
            $img_cuatro = "default.jpg";
            if (isset($uploadedFiles["img-cuatro"])) {
                // handle single input with single file upload
                $uploadedFile = $uploadedFiles["img-cuatro"];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile->getSize() <= $tamanio_maximo) {
                        if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                            $img_cuatro = moveUploadedFile($directory, $uploadedFile, 0, 3);
                        }
                    }
                }
            }
 
            $parsedBody["foto_uno"] = $img_uno;
            $parsedBody["foto_dos"] = $img_dos;
            $parsedBody["foto_tres"] = $img_tres;
            $parsedBody["foto_cuatro"] = $img_cuatro;

            $respuesta = dbPostWithData("arboles", $parsedBody);
            $respuesta->foto_uno = $img_uno;
            $respuesta->foto_dos = $img_dos;
            $respuesta->foto_tres = $img_tres;
            $respuesta->foto_cuatro = $img_cuatro;
            return $response
                ->withStatus(201) //Created
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        } else { //Disabled
            $resperr = new stdClass();
            $resperr->err = true;
            $resperr->errMsg = "Fecha no válida";
            $resperr->errMsgs = ["Fecha no válida"];
            return $response
                ->withStatus(409) //Conflicto
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
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

//[PATCH]

//Guardar los cambios de un Arbol (El metodo es post debido a las imàgenes [No funciona con partch si tiene imagenes])

$app->post("/arbol/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "nombre_popular" => array(
            "min" => 3,
            "max" => 50,
            "tag" => "Nombre popular"
        ),
        "nombre_cientifico" => array(
            "min" => 3,
            "max" => 50,
            "tag" => "Nombre cientifico"
        ),
        "origen" => array(
            "min" => 3,
            "max" => 75,
            "tag" => "Origen"
        ),
        "descripcion" => array(
            "min" => 3,
            "tag" => "Descripcion"
        ),
        "descripcionHTML" => array(
            "tag" => "descripcionHTML"
        )
    );

    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        $directory = $this->get("upload_directory_arboles");
        $tamanio_maximo = $this->get("max_file_size");
        $formatos_permitidos = $this->get("allow_file_format");
        $uploadedFiles = $request->getUploadedFiles();
        //img-uno
        $img_uno = $parsedBody["foto_uno"];
        if (isset($uploadedFiles["img-uno"])) {
            $uploadedFile = $uploadedFiles["img-uno"];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                if ($uploadedFile->getSize() <= $tamanio_maximo) {
                    if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                        $img_uno = moveUploadedFile($directory, $uploadedFile, 0, 0);
                        if ($img_uno == true) {
                            //Eliminar la vieja imagen uno si no es default.jpg
                            $eliminar = $parsedBody["foto_uno"];
                            if ($eliminar != "default.jpg") {
                                @unlink($this->get("upload_directory_arboles") . "\\$eliminar");
                            }
                        }
                    }
                }
            }
        }
        //img-dos
        $img_dos = $parsedBody["foto_dos"];
        if (isset($uploadedFiles["img-dos"])) {
            // handle single input with single file upload
            $uploadedFile = $uploadedFiles["img-dos"];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                if ($uploadedFile->getSize() <= $tamanio_maximo) {
                    if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                        $img_dos = moveUploadedFile($directory, $uploadedFile, 0, 1);
                        if ($img_dos == true) {
                            //Eliminar la vieja imagen dos si no es default.jpg
                            $eliminar = $parsedBody["foto_dos"];
                            if ($eliminar != "default.jpg") {
                                @unlink($this->get("upload_directory_arboles") . "\\$eliminar");
                            }
                        }
                    }
                }
            }
        }
         //img-tres
         $img_tres = $parsedBody["foto_tres"];
         if (isset($uploadedFiles["img-tres"])) {
             // handle single input with single file upload
             $uploadedFile = $uploadedFiles["img-tres"];
             if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                 if ($uploadedFile->getSize() <= $tamanio_maximo) {
                     if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                         $img_tres = moveUploadedFile($directory, $uploadedFile, 0, 2);
                         if ($img_tres == true) {
                             //Eliminar la vieja imagen dos si no es default.jpg
                             $eliminar = $parsedBody["foto_tres"];
                             if ($eliminar != "default.jpg") {
                                 @unlink($this->get("upload_directory_arboles") . "\\$eliminar");
                             }
                         }
                     }
                 }
             }
         }
          //img-cuatro
        $img_cuatro = $parsedBody["foto_cuatro"];
        if (isset($uploadedFiles["img-cuatro"])) {
            // handle single input with single file upload
            $uploadedFile = $uploadedFiles["img-cuatro"];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                if ($uploadedFile->getSize() <= $tamanio_maximo) {
                    if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                        $img_cuatro = moveUploadedFile($directory, $uploadedFile, 0, 3);
                        if ($img_cuatro == true) {
                            //Eliminar la vieja imagen dos si no es default.jpg
                            $eliminar = $parsedBody["foto_cuatro"];
                            if ($eliminar != "default.jpg") {
                                @unlink($this->get("upload_directory_arboles") . "\\$eliminar");
                            }
                        }
                    }
                }
            }
        }

        $parsedBody["foto_uno"] = $img_uno;
        $parsedBody["foto_dos"] = $img_dos;
        $parsedBody["foto_tres"] = $img_tres;
        $parsedBody["foto_cuatro"] = $img_cuatro;
        //Eliminar de $parsedBody id
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("arboles", $args["id"], $parsedBody);
        $respuesta->foto_uno = $img_uno;
        $respuesta->foto_dos = $img_dos;
        $respuesta->foto_tres = $img_tres;
        $respuesta->foto_cuatro = $img_cuatro;

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

//[DELETE]

//Eliminar un Árbol
$app->delete("/arbol/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $archivo = dbGet("SELECT foto_uno, foto_dos ,foto_tres, foto_cuatro FROM arboles WHERE id = " . $args["id"]);
    if ($archivo->err == false && $archivo->data["count"] > 0) {
        $fileX = $archivo->data["registros"][0]->foto_uno;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_arboles") . "\\$fileX");
        }
        $fileX = $archivo->data["registros"][0]->foto_dos;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_arboles") . "\\$fileX");
        }
        $fileX = $archivo->data["registros"][0]->foto_tres;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_arboles") . "\\$fileX");
        }
        $fileX = $archivo->data["registros"][0]->foto_cuatro;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_arboles") . "\\$fileX");
        }
        $fileX = $archivo->data["registros"][0]->foto_cinco;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_arboles") . "\\$fileX");
        }
    }
    $respuesta = dbDelete("arboles", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
