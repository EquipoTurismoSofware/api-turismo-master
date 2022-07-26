<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



//[GET]

//Obtener todas las fotos 
$app->get("/fotos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM galeria_localidades ORDER BY idlocalidad DESC";
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
$app->get("/galeria_localidades/{busqueda:[A-z]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gl.id id, gl.imagen img, d.nombre localidad, c.nombre ciudad, d.toplocalidad toplocalidad, t.nombre tag";
    $xSQL .= "FROM galeria_localidades gl"; 
    $xSQL .= "JOIN departamentos d";
    $xSQL .= "JOIN gal_tag gt";
    $xSQL .= "JOIN tag t";
    $xSQL .= "JOiN ciudades c";
    $xSQL .= "WHERE gl.idloc = d.id";
    $xSQL .= "AND gl.id = gt.id_img";
    $xSQL .= "AND gt.id_tag = t.id";
    $xSQL .= "AND gl.idciudad = c.id";
    $xSQL .= "AND (c.nombre LIKE '%" . $args["busqueda"] . "%'";
    $xSQL .= "OR d.nombre LIKE '%" . $args["busqueda"] . "%'";
    $xSQL .= "OR t.nombre LIKE '%" . $args["busqueda"] . "%')";
    $xSQL .= "ORDER BY d.nombre, d.toplocalidad DESC";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Obtener las últimas fotos cargadas
$app->get("/galeria_localidades", function (Request $request, Response $response, array $args) {
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


//[POST]

//Agregar una imagen
$app->post("/addfotoloc", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "mayorcero" => true,
            "numeric" => true,
            "tag" => "Identificador de Localidad"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody())) {
        $parsedBody = $request->getParsedBody();
        //Imágenes
        $directory = $this->get("upload_directory_galeriaLocalidad");
        $tamanio_maximo = $this->get("max_file_size");
        $formatos_permitidos = $this->get("allow_file_format");
        $uploadedFiles = $request->getUploadedFiles();
        //img-uno
        $img_uno = "default.jpg";
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

        $parsedBody["image"] = $img_uno;
        $respuesta = dbPostWithData("galeria_localidades", $parsedBody);
        $respuesta->image = $img_uno;
        return $response
            ->withStatus(201) //Created
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
});


// DELETE 

//Eliminar un Tag de una imagen  ** Ojo espera el id de la tabla guiaservicios, no el id del servicio que es otra cosa.
$app->delete("/foto/tag/{idTagFoto:[0-9]+}", function (Request $request, Response $response, array $args) {
    $respuesta = dbDelete("gal_tag", $args["idTagFoto"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

?>