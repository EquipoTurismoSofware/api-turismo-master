<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



//[GET]

//Obtener todas las fotos 
$app->get("/fotos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM galeria_localidades ORDER BY idloc DESC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener los últimas fotos especidicando una cantidad especifica d registros.
$app->get("/listfotos/{cantidad:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM galeria_localidades ORDER BY id DESC LIMIT 0, " . $args["cantidad"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener los tags de una imagen en particular
$app->get("/foto/{id:[0-9]+}/tag", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT  gal_tag.*, tag.nombre  FROM gal_tag ";
    $xSQL .= " INNER JOIN  tag ON gal_tag.id = tag.id ";
    $xSQL .= " WHERE gal_tag.id_img = " . $args["id"];
    $xSQL .= " ORDER BY tag.nombre";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener las últimas fotos  buscadas
$app->get("/buscaGaleria/{busqueda:[a-zA-Z]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gl.id id, gl.imagen img, d.nombre localidad, c.nombre ciudad, d.toplocalidad toplocalidad, t.nombre tag";
    $xSQL .= " FROM galeria_localidades gl"; 
    $xSQL .= " JOIN departamentos d";
    $xSQL .= " JOIN gal_tag gt";
    $xSQL .= " JOIN tag t";
    $xSQL .= " JOIN ciudades c";
    $xSQL .= " WHERE gl.idloc = d.id";
    $xSQL .= " AND gl.id = gt.id_img";
    $xSQL .= " AND gt.id_tag = t.id";
    $xSQL .= " AND gl.idciudad = c.id";
    $xSQL .= " AND (c.nombre LIKE '%" . $args["busqueda"] . "%'";
    $xSQL .= " OR d.nombre LIKE '%" . $args["busqueda"] . "%'";
    $xSQL .= " OR t.nombre LIKE '%" . $args["busqueda"] . "%')";
    $xSQL .= " ORDER BY d.nombre, d.toplocalidad DESC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Obtener las últimas fotos cargadas
$app->get("/galeria_localidad", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gl.id id, gl.imagen img, d.nombre localidad, c.nombre ciudad, d.toplocalidad toplocalidad, t.nombre tag
    FROM galeria_localidades gl 
    JOIN departamentos d
    JOIN gal_tag gt
    JOIN tag t
    JOiN ciudades c
    WHERE gl.idloc = d.id
    AND gl.id = gt.id_img
    AND gt.id_tag = t.id
    AND gl.idciudad = c.id
    ORDER BY d.nombre, d.toplocalidad DESC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Obtener los datos de una deteriminada foto de la galeria
$app->get("/galerialocalidad/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM galeria_localidades WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

// Filtra el nombre de las ciudades, departamentos y tag 
//
$app->get("/filtraCDT/{id:[a-zA-Z]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT C.nombre, D.nombre, T.nombre FROM ciudades AS C, departamentos AS D, tag AS T";
    $xSQL .= " WHERE C.nombre LIKE  '%" .$args["id"] . "%'";
    $xSQL .= " AND D.nombre LIKE '%" . $args["id"] . "%'";
    $xSQL .= " AND T.nombre LIKE '%" . $args["id"] . "%'";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


//[POST]

//Agregar un una nueva foto
$app->post("/addfotoloc", function (Request $request, Response $response, array $args) {
    $reglas = array(

     );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
            //Imágenes
            $directory = $this->get("upload_directory_galeriaLocalidad");
            $tamanio_maximo = $this->get("max_file_size");
            $formatos_permitidos = $this->get("allow_file_format");
            $uploadedFiles = $request->getUploadedFiles();
            //img-uno
            $imagen = "default.jpg";
            if (isset($uploadedFiles["imagen"])) {
                // handle single input with single file upload
                $uploadedFile = $uploadedFiles["imagen"];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    if ($uploadedFile->getSize() <= $tamanio_maximo) {
                        if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                            $imagen = moveUploadedFile($directory, $uploadedFile, 0, 0);
                        }
                    }
                }
            }
 
            $parsedBody["imagen"] = $imagen;

            $respuesta = dbPostWithData("galeria_localidades", $parsedBody);
            $respuesta->imagen = $imagen;
     
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


//Agregar una imagen
// $app->post("/addfotoloc/{idLoc:[0-9]+}", function (Request $request, Response $response, array $args) {
//     $reglas = array(
//         "idlocalidad" => array(
//             //"mayorcero" => true,
//             "numeric" => true,
//             //"tag" => "Identificador de Localidad"
//         )
//     );
//     $validar = new Validate();
//     if ($validar->validar($request->getParsedBody())) {
//         $parsedBody = $request->getParsedBody();
//         //Imágenes
//         $directory = $this->get("upload_directory_galeriaLocalidad");
//         $tamanio_maximo = $this->get("max_file_size");
//         $formatos_permitidos = $this->get("allow_file_format");
//         $uploadedFiles = $request->getUploadedFiles();
//         //img-uno
//         $img_uno = "default.jpg";
//         if (isset($uploadedFiles["img-uno"])) {
//             $uploadedFile = $uploadedFiles["img-uno"];
//             if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
//                 if ($uploadedFile->getSize() <= $tamanio_maximo) {
//                     if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
//                         $img_uno = moveUploadedFile($directory, $uploadedFile, 0, 0);
//                     }
//                 }
//             }
//         }

//         $parsedBody["image"] = $img_uno;
//         $respuesta = dbPostWithData("galeria_localidades", $parsedBody);
//         $respuesta->image = $img_uno;
//         return $response
//             ->withStatus(201) //Created
//             ->withHeader("Content-Type", "application/json")
//             ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
//     }
// });



// DELETE 

//Eliminar un Tag de una imagen  ** Ojo espera el id de la tabla guiaservicios, no el id del servicio que es otra cosa.
$app->delete("/foto/tag/{idTagFoto:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("gal_tag", $args["idTagFoto"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Eliminar un Árbol
$app->delete("/delfotoloc/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $archivo = dbGet("SELECT imagen FROM arboles WHERE id = " . $args["id"]);
    if ($archivo->err == false && $archivo->data["count"] > 0) {
        $fileX = $archivo->data["registros"][0]->imagen;
        if ($fileX != "default.jpg") {
            @unlink($this->get("upload_directory_galeriaLocalidad") . "\\$fileX");
        }
    }
    $respuesta = dbDelete("galeria_localidades", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


?>