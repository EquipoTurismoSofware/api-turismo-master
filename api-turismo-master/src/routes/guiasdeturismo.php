<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//GETS
$app->get("/guiasturismo/ciudades", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT DISTINCT ciudades.foto, ciudades.nombre AS ciudad, ciudades.id, zonas_ciudades.idzona AS ZonaId FROM guias_turismo";
    $xSQL .= " INNER JOIN ciudades ON guias_turismo.idciudad = ciudades.id";
    $xSQL .= " INNER JOIN zonas_ciudades ON ciudades.id= zonas_ciudades.idciudad";
    $xSQL .= " WHERE guias_turismo.adhiereCovid > 0";
    $xSQL .= " ORDER BY ciudades.id";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Areas de servicio
$app->get("/areasServicio", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM areas_servicio";

    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Busqueda de areas de servicio de un guia
$app->get("/guiasturismo/areas/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT guias_areas.id, areas_servicio.nombre FROM guias_areas";
    $xSQL .= " INNER JOIN guias_turismo ON guias_turismo.id = guias_areas.idGuia";
    $xSQL .= " INNER JOIN areas_servicio ON areas_servicio.id = guias_areas.idArea" ;
    $xSQL .= " WHERE guias_areas.idGuia = " . $args["id"];

    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
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

//--- Datos de todos los guias que adhieren DOSEP --//

$app->get("/guiasturismo/adhiereDosep", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT guias_turismo.id, guias_turismo.nombre, categoria, legajo, ambito, telefono, correo, ciudades.nombre as ciudad FROM guias_turismo";
    $xSQL .= " INNER JOIN ciudades ON ciudades.id = guias_turismo.idciudad";
    $xSQL .= " WHERE guias_turismo.adhiereDosep > 0";
    $xSQL .= " ORDER BY ciudades.nombre";   
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/guiasturismo/adhiereCovid", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT guias_turismo.id, guias_turismo.nombre, categoria, legajo, ambito, telefono, correo, ciudades.nombre as ciudad FROM guias_turismo";
    $xSQL .= " INNER JOIN ciudades ON ciudades.id = guias_turismo.idciudad";
    $xSQL .= " WHERE guias_turismo.adhiereCovid > 0";
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

//AGREGAR UN GUIA A UNA AREA
$app->post("/guiasareas", function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();

    $respuesta = dbPostWithData("guias_areas", $parsedBody);

    return $response
        ->withStatus(200) 
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/guiasturismox/new", function (Request $request, Response $response, array $args) {
        $parsedBody = $request->getParsedBody();
        $fecha_valida = false;
        $fecha_valida2 = false;

        if (strpos($parsedBody["fechNac"], "-") !== false && strpos($parsedBody["fechUltimaRenovacion"], "-") !== false) {
            $data_fechaNac = explode("-", $parsedBody["fechNac"]);
            $data_fechaRen = explode("-", $parsedBody["fechUltimaRenovacion"]);
            //YYYY-MM-DD
            if (count($data_fechaNac) == 3 && count($data_fechaRen) == 3) {
                $fecha_valida = checkdate($data_fechaNac[1], $data_fechaNac[2], $data_fechaNac[0]);
                $fecha_valida2 = checkdate($data_fechaRen[1], $data_fechaRen[2], $data_fechaRen[0]);
            }
        }

        if ($fecha_valida == true && $fecha_valida2 == true) {
            $directory1 = $this->get("upload_directory_guias_fotoPerfil");
            $directory2 = $this->get("upload_directory_guias_capacitaciones");
            $directory3 = $this->get("upload_directory_guias_certificados");
            $directory4 = $this->get("upload_directory_guias_titulos");
            $tamanio_maximo = $this->get("max_file_size");
            $tamanio_maximo2 = $this->get("max_file_size2");
            $formatos_permitidos = $this->get("allow_file_format");
            $formatos_permitidos2 = $this->get("allow_file_format2");

            $uploadedFiles = $request->getUploadedFiles();
            
            $file = "default.jpg";
            
            if (isset($uploadedFiles["foto-file"])) {
                $uploadedFile = $uploadedFiles["foto-file"];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile->getSize() <= $tamanio_maximo) {
                        if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                            $file = moveUploadedFile($directory1, $uploadedFile, 0, 0);
                        }
                    }
                }   
            }

            $parsedBody["foto"] = $file;

            //Capacitaciones
            $file2 = "default";
            
            if (isset($uploadedFiles["capacitaciones-file"])) {
                $uploadedFile2 = $uploadedFiles["capacitaciones-file"];
                if ($uploadedFile2->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile2->getSize() <= $tamanio_maximo2) {
                        if (in_array($uploadedFile2->getClientMediaType(), $formatos_permitidos2)) {
                            $file2 = moveUploadedFile($directory2, $uploadedFile2, 0, 0);
                        }
                    }
                }
            }

            $parsedBody["capacitaciones"] = $file2;
            
            //Certificados
            $file3 = "default";
            
            if (isset($uploadedFiles["certificados-file"])) {
                $uploadedFile3 = $uploadedFiles["certificados-file"];
                if ($uploadedFile3->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile3->getSize() <= $tamanio_maximo2) {
                        if (in_array($uploadedFile3->getClientMediaType(), $formatos_permitidos2)) {
                            $file3 = moveUploadedFile($directory3, $uploadedFile3, 0, 0);
                        }
                    }
                }
            }

            $parsedBody["certificados"] = $file3;

            //Titulo
            $file4 = "default";
            
            if (isset($uploadedFiles["titulo-file"])) {
                $uploadedFile4 = $uploadedFiles["titulo-file"];
                if ($uploadedFile4->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile4->getSize() <= $tamanio_maximo2) {
                        if (in_array($uploadedFile4->getClientMediaType(), $formatos_permitidos2)) {
                            $file4 = moveUploadedFile($directory4, $uploadedFile4, 0, 0);
                        }
                    }
                }
            }

            $parsedBody["titulo"] = $file4;
            
            $respuesta = dbPostWithData("guias_turismo", $parsedBody);
            $respuesta->foto = $file;
            $respuesta->capacitaciones = $file2;
            $respuesta->certificados = $file3;
            $respuesta->titulo = $file4;

            return $response
            ->withStatus(201) //Created
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        } else {
            $resperr = new stdClass();
            $resperr->err = true;
            $resperr->errMsg = "Fecha no válida";
            $resperr->errMsgs = ["Fecha no válida"];
            return $response
                ->withStatus(409) //Conflicto
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
});

//Guardar los cambios de una un guia
$app->post("/guiasturismox/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM guias_turismo WHERE id = " . $args["id"];
    $respuestaGet = dbGet($xSQL);


    $parsedBody = $request->getParsedBody();

    $directory1 = $this->get("upload_directory_guias_fotoPerfil");
    $directory2 = $this->get("upload_directory_guias_capacitaciones");
    $directory3 = $this->get("upload_directory_guias_certificados");
    $directory4 = $this->get("upload_directory_guias_titulos");

    $uploadedFiles = $request->getUploadedFiles();
    
    $file = $respuestaGet->data["registros"][0]->foto;

    if (isset($uploadedFiles["foto-file"])) {
        $uploadedFile = $uploadedFiles["foto-file"];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $file = moveUploadedFile($directory1, $uploadedFile, 0, 0);
            if ($file == true) {
                $eliminar = $parsedBody["foto"];
                if ($eliminar != "default.jpg") {
                    @unlink($this->get("upload_directory_guias_fotoPerfil") . "\\$eliminar");
                }
                
            }
        }
    }  
    
    $parsedBody["foto"] = $file;

    //Capacitaciones
    $file2 = $respuestaGet->data["registros"][0]->capacitaciones;

    if (isset($uploadedFiles["capacitaciones-file"])) {
        $uploadedFile2 = $uploadedFiles["capacitaciones-file"];
        if ($uploadedFile2->getError() === UPLOAD_ERR_OK) {
            $file2 = moveUploadedFile($directory2, $uploadedFile2, 0, 0);
            if ($file2 == true) {
                $eliminar = $parsedBody["capacitaciones"];
                @unlink($this->get("upload_directory_guias_capacitaciones") . "\\$eliminar");
            }
        }
    }
    

    $parsedBody["capacitaciones"] = $file2;
    
    //Certificados
    $file3 = $respuestaGet->data["registros"][0]->certificados;
    
    if (isset($uploadedFiles["certificados-file"])) {
        $uploadedFile3 = $uploadedFiles["certificados-file"];
        if ($uploadedFile3->getError() === UPLOAD_ERR_OK) {
            $file3 = moveUploadedFile($directory3, $uploadedFile3, 0, 0);
            if ($file3 == true) {
                $eliminar = $parsedBody["certificados"];
                @unlink($this->get("upload_directory_guias_certificados") . "\\$eliminar");
            }
        }
    }
    

    $parsedBody["certificados"] = $file3;

    //Titulo
    $file4 = $respuestaGet->data["registros"][0]->titulo;
    
    if (isset($uploadedFiles["titulo-file"])) {
        $uploadedFile4 = $uploadedFiles["titulo-file"];
        if ($uploadedFile4->getError() === UPLOAD_ERR_OK) {
            $file4 = moveUploadedFile($directory4, $uploadedFile4, 0, 0);
            if ($file4 == true) {
                $eliminar = $parsedBody["titulo"];
                @unlink($this->get("upload_directory_guias_titulos") . "\\$eliminar");
            }
        }
    }
    

    $parsedBody["titulo"] = $file4;

    $respuesta = dbPatchWithData("guias_turismo", $args["id"], $parsedBody);
    $respuesta->foto = $file;
    $respuesta->certificados = $file2;
    $respuesta->capacitaciones = $file3;
    $respuesta->titulo = $file4;
    
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
});



//Eliminar un guia Turistico
$app->delete("/guiasturismo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $archivo = dbGet("SELECT foto, certificados, capacitaciones, titulo FROM guias_turismo WHERE id = " . $args["id"]);
    if ($archivo->err == false && $archivo->data["count"] > 0) {
        $fileX = $archivo->data["registros"][0]->foto;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_guias_fotoPerfil") . "\\$fileX");
        }

        $fileX = $archivo->data["registros"][0]->capacitaciones;
        @unlink($this->get("upload_directory_guias_capacitaciones") . "\\$fileX");
        
        $fileX = $archivo->data["registros"][0]->certificados; 
        @unlink($this->get("upload_directory_guias_certificados") . "\\$fileX");
        
        $fileX = $archivo->data["registros"][0]->titulo;
        @unlink($this->get("upload_directory_guias_titulos") . "\\$fileX");
    }
    
    $respuesta = dbDelete("guias_turismo", $args["id"]);

    $xSQL = "SELECT id FROM guias_areas WHERE idGuia = " . $args["id"];

    $respuestaGet = dbGet($xSQL);

    if ($respuestaGet->data["count"] > 0) {
        for ($i = 0; $i < count($respuestaGet->data["registros"]); $i++) {
            dbDelete("guias_areas", $respuestaGet->data["registros"][$i]->id);     
        }
    }

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Eliminar una area un guia
$app->delete("/guiasAreas/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    
    $respuesta = dbDelete("guias_areas", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
 
