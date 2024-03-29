<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Atractivos

//Datos de un Atractivo Particular
$app->get("/atractivo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM atractivos WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);
    //Color?
   
        //Redes Sociales
 
     
 
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//Datos de un gastronomia Particular
$app->get("/gastronomia/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gastronomia.*, ciudades.nombre AS ciudad FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " WHERE gastronomia.id = " .$args["id"];
    $respuesta = dbGet($xSQL);
    //Color?
    $xSQL = "SELECT imagen FROM gastronomia_imgs";
    $xSQL .= " WHERE idgastronomia = " . $respuesta->data["registros"][0]->id;
    $fotos = dbGet($xSQL);
    $respuesta->data["registros"][0]->fotos = $fotos->data["registros"];

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});



$app->get("/gastronomiaNoFotos/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM gastronomia WHERE id = " . $args["id"];
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//---- Datos de gastronomias según localidad ---
/*
$app->get("/gastronomia/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM gastronomia WHERE gastronomia.idlocalidad = " . $args["id"];
    $respuesta = dbGet($xSQL);
    //Color?
    $color = "722789"; //Violeta Oscuro

    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM gastronomia_imgs WHERE idgastronomia = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});*/

//--------------------------------------------
/*
$app->get("/gastronomia/{tipo}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gastronomia.*, ciudades.nombre AS localidad FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " WHERE gastronomia.tipo = " . $args["tipo"];
    $xSQL .= " ORDER BY gastronomia.nombre";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM gastronomia_imgs WHERE idgastronomia = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});*/
//------------------------

$app->get("/gastronomia/adhiereDosep", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gastronomia.id, gastronomia.idlocalidad, gastronomia.tipo, gastronomia.nombre, gastronomia.telefono, ciudades.caracteristica, gastronomia_imgs.imagen, ciudades.nombre AS ciudad FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN gastronomia_imgs ON gastronomia.id = gastronomia_imgs.idgastronomia";
    $xSQL .= " WHERE gastronomia.adhiereDosep > 0 AND gastronomia_imgs.numeracion = 1";
    $xSQL .= " ORDER BY ciudades.nombre";
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/gastronomia/galeriaupdate", function (Request $request, Response $response, array $args) {

    $db = new DB();
    $db->connect();

    $xSQL = "SELECT idgastronomia FROM gastronomia_imgs";
   $xSQL .= " GROUP BY idgastronomia";
   $xSQL .= " ORDER BY idgastronomia";

   $idsgaleria = dbGet($xSQL);

   for ($i = 0; $i < count($idsgaleria->data["registros"]); $i++) {
        $xSQL = "SELECT id FROM gastronomia_imgs";
        $xSQL .= " WHERE idgastronomia = ". $idsgaleria->data["registros"][$i]->idgastronomia; 
        $itemsgaleria = dbGet($xSQL);
        for ($x = 1; $x < (count($itemsgaleria->data["registros"])+1); $x++) {
            $xSQL = "UPDATE gastronomia_imgs SET numeracion = ".$x. " WHERE id = ". $itemsgaleria->data["registros"][$x-1]->id;
            $db->consultar($xSQL);
        }
    }
    $db->close();
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($idsgaleria, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
/*
//---- Datos de una gastronomia + AdhiereDOSEP + imagenes ---/
$app->get("/gastronomia/adhiereDosep", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM gastronomia WHERE gastronomia.adhiereDosep > 0";
    $respuesta = dbGet($xSQL);
    //Color?
    $color = "722789"; //Violeta Oscuro

    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM gastronomia_imgs WHERE idgastronomia = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
*/
//Datos de un gastronomia Particular
$app->get("/gastronomia/{id:[0-9]+}/zona", function (Request $request, Response $response, array $args) {
    //Ciudades de la Zona
    $xSQL = "SELECT zonas_ciudades.idciudad, ciudades.nombre as ciudad FROM zonas_ciudades";
    $xSQL .= " INNER JOIN ciudades ON zonas_ciudades.idciudad = ciudades.id";
    $xSQL .= " WHERE zonas_ciudades.idzona = " . $args["id"];
    $xSQL .= " ORDER BY ciudades.nombre";
    $ciudades_zona = dbGet($xSQL);
    $respuestaFinal = "";
    $conteo = 0;
    for ($i = 0; $i <  $ciudades_zona->data["count"]; $i++) {
        $xSQL = "SELECT * from gastronomia";
        $xSQL .= " INNER JOIN gastronomia_imgs ON gastronomia_imgs.idgastronomia = gastronomia.id";
        $xSQL .= " WHERE gastronomia.idlocalidad = " . $ciudades_zona->data["registros"][$i]->idciudad;
        $respuesta = dbGet($xSQL);
        for ($a = 0; $a <  $respuesta->data["count"]; $a++) {
            $respuestaFinal->data[$conteo]->id = $respuesta->data["registros"][$a]->idgastronomia;
            $respuestaFinal->data[$conteo]->nombre = $respuesta->data["registros"][$a]->nombre;
            $respuestaFinal->data[$conteo]->descripcion = $respuesta->data["registros"][$a]->descripcion;
            $respuestaFinal->data[$conteo]->tipo = $respuesta->data["registros"][$a]->tipo;
            $respuestaFinal->data[$conteo]->imagen = $respuesta->data["registros"][$a]->imagen;
            $conteo = $conteo + 1;
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuestaFinal, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/gastronomia/app/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gastronomia.id, ciudades.nombre AS ciudad, gastronomia.nombre, gastronomia_imgs.imagen FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN gastronomia_imgs ON gastronomia_imgs.idgastronomia = gastronomia.id";
    $xSQL .= " WHERE gastronomia_imgs.numeracion = 1 AND gastronomia.id = ". $args['id'];
    $xSQL .= " ORDER BY ciudades.nombre";
    $ciudades_zona = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($ciudades_zona, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
 });


$app->get("/gastronomia/full", function (Request $request, Response $response, array $args) {
    //Ciudades de la Zona
    $xSQL = "SELECT gastronomia.id, ciudades.nombre AS ciudad, gastronomia.nombre, gastronomia_imgs.imagen FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN gastronomia_imgs ON gastronomia_imgs.idgastronomia = gastronomia.id";
    $xSQL .= " WHERE gastronomia_imgs.numeracion = 1 ";
    $xSQL .= " ORDER BY ciudades.nombre";
    $ciudades_zona = dbGet($xSQL);

    /*for ($i = 0; $i < count($ciudades_zona->data["registros"]); $i++) {
        //Redes Sociales
 
        $xSQL = "SELECT imagen FROM gastronomia_imgs";
        $xSQL .= " WHERE idgastronomia = " . $ciudades_zona->data["registros"][$i]->id;
        $fotos = dbGet($xSQL);
        $ciudades_zona->data["registros"][$i]->fotos = $fotos->data["registros"];
    }*/


    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($ciudades_zona, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/cerveceria/app/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT  ciudades.nombre AS ciudad, gastronomia.* FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .=" WHERE (gastronomia.tipo = 'Camino de la Cerveza Artesanal de Sierras Centrales' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Los Comechingones' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Norte Puntano' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Conlara' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Circuito del Morro') AND gastronomia.id = ". $args['id'];
    $gastronomia = dbGet($xSQL);

    for ($i = 0; $i < count($gastronomia->data["registros"]); $i++) {
        $xSQL = "SELECT imagen FROM gastronomia_imgs";
        $xSQL .= " WHERE idgastronomia = " . $gastronomia->data["registros"][$i]->id;
        $fotos = dbGet($xSQL);
        $gastronomia->data["registros"][$i]->fotos = $fotos->data["registros"];
    }

    return $response
         ->withStatus(200)
         ->withHeader("Content-Type", "application/json")
         ->write(json_encode($gastronomia, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
 });

$app->get("/ceveceriafull", function (Request $request, Response $response, array $args) {


    $xSQL = "SELECT  ciudades.nombre AS ciudad, gastronomia.* FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .=" WHERE gastronomia.tipo = 'Camino de la Cerveza Artesanal de Sierras Centrales' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Los Comechingones' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Norte Puntano' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Conlara' OR gastronomia.tipo = 'Camino de la Cerveza Artesanal de Circuito del Morro'";
    $gastronomia = dbGet($xSQL);
    //Color?
    for ($i = 0; $i < count($gastronomia->data["registros"]); $i++) {
        //Redes Sociales
 
        $xSQL = "SELECT imagen FROM gastronomia_imgs";
        $xSQL .= " WHERE idgastronomia = " . $gastronomia->data["registros"][$i]->id;
        $fotos = dbGet($xSQL);
        $gastronomia->data["registros"][$i]->fotos = $fotos->data["registros"];
    }

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($gastronomia, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


//Todos los atractivos de una localidad

$app->get("/atractivos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY atractivos.nombre";
    $respuesta = dbGet($xSQL);
   
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//----------------

$app->get("/atractivos/all", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY atractivos.nombre";
    $respuesta = dbGet($xSQL);
   
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Trae atractivos solo de ciertos tipos
$app->get("/atractivos/algunosTipos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.id, atractivos.idlocalidad, clasificacion.nombre AS tipoAtractivo, atractivos.nombre, atractivos.descripcion, atractivos.latitud, atractivos.longitud, ciudades.nombre AS ciudad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN clasificacion ON atractivos.idTipo = clasificacion.id";
    //$xSQL .= " WHERE clasificacion.nombre = 'Diques' OR clasificacion.nombre = 'Ríos y saltos de agua' OR clasificacion.nombre = 'Lagunas' OR clasificacion.nombre = 'Parques' OR clasificacion.nombre = 'Balnearios' OR clasificacion.nombre = 'Museos' OR clasificacion.nombre = 'Cerros' OR clasificacion.nombre = 'Caminos Pintorescos'";
    $xSQL .= " ORDER BY atractivos.nombre";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM atractivo_imgs WHERE idatractivo = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//
$app->get("/atractivos/onlyTipos", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT DISTINCT tipo FROM atractivos";
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Todos los gastronomicos de una localidad

$app->get("/gastronomia", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gastronomia.*, ciudades.nombre AS localidad FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " ORDER BY gastronomia.nombre";
    $respuesta = dbGet($xSQL);
   
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//----------------

$app->get("/clasificacion/all", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM clasificacion";
    $respuesta = dbGet($xSQL);

    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//DATOS DE UN TIPO EN ESPECIAL 
$app->get("/atractivoTipo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad, clasificacion.nombre AS tipoNombre FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " INNER JOIN clasificacion ON atractivos.idTipo = clasificacion.id";
    $xSQL .= " WHERE atractivos.idTipo = " . $args['id'];
    $xSQL .= " ORDER BY atractivos.nombre";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM atractivo_imgs WHERE idatractivo = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/atractivo/{tipo}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " WHERE atractivos.tipo = " . $args["tipo"];
    $xSQL .= " ORDER BY atractivos.nombre";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM atractivo_imgs WHERE idatractivo = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->get("/gastronomia/{tipo}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT gastronomia.*, ciudades.nombre AS localidad FROM gastronomia";
    $xSQL .= " INNER JOIN ciudades ON gastronomia.idlocalidad = ciudades.id";
    $xSQL .= " WHERE gastronomia.tipo = " . $args["tipo"];
    $xSQL .= " ORDER BY gastronomia.nombre";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM gastronomia_imgs WHERE idgastronomia = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Todos los Imperdibles del producto CREER 
$app->get("/atractivo/creer/{numero}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " WHERE atractivos.imperdible >= " . $args["numero"];
    $xSQL .= " ORDER BY atractivos.imperdible";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM atractivo_imgs WHERE idatractivo = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


//Todos los Imperdibles del producto CREER 
$app->get("/atractivo/moto/{numero}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " WHERE atractivos.zonaMoto = " . $args["numero"];
    $xSQL .= " ORDER BY atractivos.zonaMoto";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM atractivo_imgs WHERE idatractivo = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


//Todos los Imperdibles del producto CREER 
$app->get("/zona/{numero}/creer/{tipo}", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT atractivos.*, ciudades.nombre AS localidad FROM atractivos";
    $xSQL .= " INNER JOIN ciudades ON atractivos.idlocalidad = ciudades.id";
    $xSQL .= " WHERE atractivos.tipo = " . $args["tipo"];
    $xSQL .= " ORDER BY atractivos.nombre";
    $respuesta = dbGet($xSQL);

    $color = "722789"; //Violeta Oscuro
    //Para obtener el color (saber si la localidad es parte de alguna zona)
    if ($respuesta->data["count"] > 0) {
        for ($i = 0; $i <  $respuesta->data["count"]; $i++) {
            $xSQL = "SELECT color from zonas";
            $xSQL .= " INNER JOIN zonas_ciudades ON zonas.id = zonas_ciudades.idzona";
            $xSQL .= " WHERE zonas_ciudades.idciudad = " . $respuesta->data["registros"][$i]->idlocalidad;
            $color = dbGet($xSQL);
            if ($color->data["count"] > 0) {
                $respuesta->data["registros"][$i]->color = $color->data["registros"][0]->color;
            } else { //No pertenece a una zona
                $respuesta->data["registros"][$i]->color = "722789";
            }
        }
    }

    //Imagenes del Atractivo
    for ($i = 0; $i < count($respuesta->data["registros"]); $i++) {
        $respuesta->data["registros"][$i]->color = $color; //Set de color
        $xSQL = "SELECT imagen FROM atractivo_imgs WHERE idatractivo = " . $respuesta->data["registros"][$i]->id;
        $imagenes = dbGet($xSQL);
        if ($imagenes->data["count"] > 0) {
            $respuesta->data["registros"][$i]->imagenes = $imagenes->data["registros"];
        } else {
            $respuesta->data["registros"][$i]->imagenes = [array("imagen" => "default.jpg")];
        }
    }
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});



//Imagenes de un atractivo Particular
$app->get("/atractivo/{id:[0-9]+}/imagenes", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM atractivo_imgs WHERE idatractivo = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Imagenes de un gastronomico Particular
$app->get("/gastronomia/{id:[0-9]+}/imagenes", function (Request $request, Response $response, array $args) {
    $xSQL = "SELECT * FROM gastronomia_imgs WHERE idgastronomia = " . $args["id"];
    $respuesta = dbGet($xSQL);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//[POST]

//Agregar un Atractivo
$app->post("/atractivo/new/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "mayorcero" => true,
            "numeric" => true,
            "tag" => "Identificador de Localidad"
        ),
        "idTipo" => array(
            "numeric" => true,
            "tag" => "idTipo"
        ),
        "tipo" => array(
            "max" => 150,
            "tag" => "Tipo de Atractivo"
        ),
        "nombre" => array(
            "min" => 5,
            "max" => 50,
            "tag" => "Nombre del Atractivo"
        ),
        "domicilio" => array(
            "max" => 50,
            "tag" => "Domicilio del Atractivo"
        ),
        "descripcion" => array(
            "tag" => "Descripcion del Atractivo"
        ),
        "latitud" => array(
            "numeric" => true,
            "tag" => "Latitud"
        ),
        "longitud" => array(
            "numeric" => true,
            "tag" => "Longitud"
        ),
        "latitudg" => array(
            "max" => 25,
            "tag" => "Latitud º"
        ),
        "longitudg" => array(
            "max" => 25,
            "tag" => "Longitud º"
        ),
        "telefono" => array(
            "max" => 25,
            "tag" => "Teléfono"
        ),
        "mail" => array(
            "max" => 100,
            "tag" => "Email"
        ),
        "web" => array(
            "max" => 100,
            "tag" => "Web"
        ),
        "costo" => array(
            "numeric" => true,
            "tag" => "Costo"
        ),
        "lunes" => array(
            "max" => 100,
            "tag" => "Horario Lunes"
        ),
        "martes" => array(
            "max" => 100,
            "tag" => "Horario Martes"
        ),
        "miercoles" => array(
            "max" => 100,
            "tag" => "Horario Miércoles"
        ),
        "jueves" => array(
            "max" => 100,
            "tag" => "Horario Jueves"
        ),
        "viernes" => array(
            "max" => 100,
            "tag" => "Horario Viernes"
        ),
        "sabado" => array(
            "max" => 100,
            "tag" => "Horario Sábado"
        ),
        "domingo" => array(
            "max" => 100,
            "tag" => "Horario Domingo"
        ),
        "imperdible" => array(
            "tag" => "Imperdible"
        ),
        "zonaMoto" => array(
            "tag" => "zonaMoto"
        )
        ,
        "activo" => array(
            "tag" => "activo"
        )
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        $respuesta = dbPostWithData("atractivos", $parsedBody);
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
//[POST]

//Agregar un nuevo espacio Gastronomico
$app->post("/gastronomia/new/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "mayorcero" => true,
            "numeric" => true,
            "tag" => "Identificador de Localidad"
        ),
        "tipo" => array(
            "max" => 150,
            "tag" => "Tipo de Atractivo"
        ),
        "nombre" => array(
            "min" => 5,
            "max" => 150,
            "tag" => "Nombre del Atractivo"
        ),
        "domicilio" => array(
            "max" => 200,
            "tag" => "Domicilio del Atractivo"
        ),
        "descripcion" => array(
            "tag" => "Descripcion del Atractivo"
        ),
        "latitud" => array(
            "numeric" => true,
            "tag" => "Latitud"
        ),
        "longitud" => array(
            "numeric" => true,
            "tag" => "Longitud"
        ),
        "latitudg" => array(
            "max" => 25,
            "tag" => "Latitud º"
        ),
        "longitudg" => array(
            "max" => 25,
            "tag" => "Longitud º"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "Teléfono"
        ),
        "mail" => array(
            "max" => 100,
            "tag" => "Email"
        ),
        "web" => array(
            "max" => 100,
            "tag" => "Web"
        ),
        "costo" => array(
            "numeric" => true,
            "tag" => "Costo"
        ),
        "lunes" => array(
            "max" => 100,
            "tag" => "Horario Lunes"
        ),
        "martes" => array(
            "max" => 100,
            "tag" => "Horario Martes"
        ),
        "miercoles" => array(
            "max" => 100,
            "tag" => "Horario Miércoles"
        ),
        "jueves" => array(
            "max" => 100,
            "tag" => "Horario Jueves"
        ),
        "viernes" => array(
            "max" => 100,
            "tag" => "Horario Viernes"
        ),
        "sabado" => array(
            "max" => 100,
            "tag" => "Horario Sábado"
        ),
        "domingo" => array(
            "max" => 100,
            "tag" => "Horario Domingo"
        ),
        "imperdible" => array(
            "tag" => "Imperdible"
        ),
        "adhiereDosep" => array(
            "tag" => "Adhiere Dosep"
        ),
    );
    $validar = new Validate();
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        $parsedBody = $request->getParsedBody();
        $respuesta = dbPostWithData("gastronomia", $parsedBody);
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



//Agregar una imagen a un Atractivo en particular
$app->post("/atractivo/{id:[0-9]+}/imagen", function (Request $request, Response $response, array $args) {
    $resperr = new stdClass();
    $resperr->err = true;
    $directory = $this->get("upload_directory_atractivo");
    $tamanio_maximo = $this->get("max_file_size");
    $formatos_permitidos = $this->get("allow_file_format");
    $uploadedFiles = $request->getUploadedFiles();
    if (isset($uploadedFiles["imgup"])) {
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles["imgup"];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            if ($uploadedFile->getSize() <= $tamanio_maximo) {
                if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                    $filename = moveUploadedFile($directory, $uploadedFile, 0, $args["id"]);
                    $data = array(
                        "idatractivo" => $args["id"],
                        "imagen" => $filename
                    );
                    $respuesta = dbPostWithData("atractivo_imgs", $data);
                    if (!$respuesta->err) {
                        return $response
                            ->withStatus(201)
                            ->withHeader("Content-Type", "application/json")
                            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    } else {
                        return $response
                            ->withStatus(409) //Conflicto
                            ->withHeader("Content-Type", "application/json")
                            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    }
                } else {
                    $resperr->errMsg = "No es un formato de imagen admitido.";
                }
            } else {
                $resperr->errMsg = "La imagen no debe superar los 4 MB.";
            }
        }
    } else {
        $resperr->errMsg = "No se suministro ninguna imagen.";
    }
    return $response
        ->withStatus(409) //Conflicto
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});




//Agregar una imagen a un gastronomico en particular
$app->post("/gastronomia/{id:[0-9]+}/imagen", function (Request $request, Response $response, array $args) {
    $resperr = new stdClass();
    $resperr->err = true;
    $directory = $this->get("upload_directory_atractivo");
    $tamanio_maximo = $this->get("max_file_size");
    $formatos_permitidos = $this->get("allow_file_format");
    $uploadedFiles = $request->getUploadedFiles();
    if (isset($uploadedFiles["imgup"])) {
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles["imgup"];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            if ($uploadedFile->getSize() <= $tamanio_maximo) {
                if (in_array($uploadedFile->getClientMediaType(), $formatos_permitidos)) {
                    $filename = moveUploadedFile($directory, $uploadedFile, 0, $args["id"]);
                    $data = array(
                        "idgastronomia" => $args["id"],
                        "imagen" => $filename
                    );
                    $respuesta = dbPostWithData("gastronomia_imgs", $data);
                    if (!$respuesta->err) {
                        return $response
                            ->withStatus(201)
                            ->withHeader("Content-Type", "application/json")
                            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    } else {
                        return $response
                            ->withStatus(409) //Conflicto
                            ->withHeader("Content-Type", "application/json")
                            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    }
                } else {
                    $resperr->errMsg = "No es un formato de imagen admitido.";
                }
            } else {
                $resperr->errMsg = "La imagen no debe superar los 4 MB.";
            }
        }
    } else {
        $resperr->errMsg = "No se suministro ninguna imagen.";
    }
    return $response
        ->withStatus(409) //Conflicto
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
//[PATCH]

//Actualizar los datos de un atractivo
$app->post("/atractivo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "idlocalidad" => array(
            "numeric" => true,
            "tag" => "Identificador de Localidad"
        ),
        "idTipo" => array(
            "max" => 150,
            "tag" => "Id Tipo de Atractivo"
        ),
        "tipo" => array(
            "max" => 150,
            "tag" => "Tipo de Atractivo"
        ),
        "nombre" => array(
            "min" => 5,
            "max" => 50,
            "tag" => "Nombre del Atractivo"
        ),
        "domicilio" => array(
            "max" => 50,
            "tag" => "Domicilio del Atractivo"
        ),
        "descripcion" => array(
            "tag" => "Descripcion del Atractivo"
        ),
        "latitud" => array(
            "numeric" => true,
            "tag" => "Latitud"
        ),
        "longitud" => array(
            "numeric" => true,
            "tag" => "Longitud"
        ),
        "latitudg" => array(
            "max" => 25,
            "tag" => "Latitud º"
        ),
        "longitudg" => array(
            "max" => 25,
            "tag" => "Longitud º"
        ),
        "telefono" => array(
            "max" => 25,
            "tag" => "Teléfono"
        ),
        "mail" => array(
            "max" => 100,
            "tag" => "Email"
        ),
        "web" => array(
            "max" => 100,
            "tag" => "Web"
        ),
        "costo" => array(
            "numeric" => true,
            "tag" => "Costo"
        ),
        "lunes" => array(
            "max" => 100,
            "tag" => "Horario Lunes"
        ),
        "martes" => array(
            "max" => 100,
            "tag" => "Horario Martes"
        ),
        "miercoles" => array(
            "max" => 100,
            "tag" => "Horario Miércoles"
        ),
        "jueves" => array(
            "max" => 100,
            "tag" => "Horario Jueves"
        ),
        "viernes" => array(
            "max" => 100,
            "tag" => "Horario Viernes"
        ),
        "sabado" => array(
            "max" => 100,
            "tag" => "Horario Sábado"
        ),
        "domingo" => array(
            "max" => 100,
            "tag" => "Horario Domingo"
        ),
        "imperdible" => array(
            "tag" => "Imperdible"
        ),
        "zonaMoto" => array(
            "tag" => "zonaMoto"
        )
    );
    $validar = new Validate();
    
    if ($validar->validar($request->getParsedBody(), $reglas)) {
        //Audio
        $parsedBody = $request->getParsedBody();
        $directory = $this->get("upload_directory_audios");
        
        $uploadedFiles = $request->getUploadedFiles();
        $file = $parsedBody["audio"];

        if (isset($uploadedFiles["file-uno"])) {
            $uploadedFile = $uploadedFiles["file-uno"];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $file = moveUploadedFile($directory, $uploadedFile, 0, 0);
                if ($file == true) {
                    $eliminar = $parsedBody["audio"];
                    @unlink($this->get("upload_directory_audios") . "\\$eliminar");
                }
            }
        }
        $parsedBody["audio"] = $file;
        unset($parsedBody["id"]);
        $respuesta = dbPatchWithData("atractivos", $args["id"], $parsedBody);
        $respuesta->audio = $file;

        return $response
            ->withStatus(200) //Ok
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


//Actualizar los datos de un gastronomico
$app->patch("/gastronomia/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $reglas = array(
        "id" => array(
            "mayorcero" => true,
            "numeric" => true,
            "tag" => "Identificador de Atractivo"
        ),
        "idlocalidad" => array(
            "mayorcero" => true,
            "numeric" => true,
            "tag" => "Identificador de Localidad"
        ),
        "tipo" => array(
            "max" => 150,
            "tag" => "Tipo de Atractivo"
        ),
        "nombre" => array(
            "min" => 5,
            "max" => 50,
            "tag" => "Nombre del Atractivo"
        ),
        "domicilio" => array(
            "max" => 200,
            "tag" => "Domicilio del Atractivo"
        ),
        "descripcion" => array(
            "tag" => "Descripcion del Atractivo"
        ),
        "latitud" => array(
            "numeric" => true,
            "tag" => "Latitud"
        ),
        "longitud" => array(
            "numeric" => true,
            "tag" => "Longitud"
        ),
        "latitudg" => array(
            "max" => 25,
            "tag" => "Latitud º"
        ),
        "longitudg" => array(
            "max" => 25,
            "tag" => "Longitud º"
        ),
        "telefono" => array(
            "max" => 150,
            "tag" => "Teléfono"
        ),
        "mail" => array(
            "max" => 100,
            "tag" => "Email"
        ),
        "web" => array(
            "max" => 100,
            "tag" => "Web"
        ),
        "costo" => array(
            "numeric" => true,
            "tag" => "Costo"
        ),
        "lunes" => array(
            "max" => 100,
            "tag" => "Horario Lunes"
        ),
        "martes" => array(
            "max" => 100,
            "tag" => "Horario Martes"
        ),
        "miercoles" => array(
            "max" => 100,
            "tag" => "Horario Miércoles"
        ),
        "jueves" => array(
            "max" => 100,
            "tag" => "Horario Jueves"
        ),
        "viernes" => array(
            "max" => 100,
            "tag" => "Horario Viernes"
        ),
        "sabado" => array(
            "max" => 100,
            "tag" => "Horario Sábado"
        ),
        "domingo" => array(
            "max" => 100,
            "tag" => "Horario Domingo"
        ),        
        "adhiereDosep" => array(
            "tag" => "adhiereDosep"
        ),
        "imperdible" => array(
            "tag" => "Imperdible"
        )
    );
    $validar = new Validate();
    $parsedBody = $request->getParsedBody();
    if ($validar->validar($parsedBody, $reglas)) {
        //$respuesta = dbPatchWithData("atractivos", $args["id"], $parsedBody);
        $respuesta = dbPatchWithData("gastronomia", $args["id"], $parsedBody);
        return $response
            ->withStatus(200) //Ok
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




//[DELETE]

//Eliminar un atractivo
$app->delete("/atractivo/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    //Eliminar las imagenes del directorio y los registros de la tabla atractivo_imgs
    $xSQL = "SELECT id, imagen FROM atractivo_imgs WHERE idatractivo = " . $args["id"];
    $imagenes = dbGet($xSQL);
    if ($imagenes->data["count"] > 0) {
        for ($i = 0; $i < count($imagenes->data["registros"]); $i++) {
            if ($imagenes->data["registros"][$i]->imagen <> "default.jpg") {
                $fileX = $imagenes->data["registros"][$i]->imagen;
                @unlink($this->get("upload_directory_atractivo") . "\\$fileX");
            }
        }
        dbDelete("atractivo_imgs", $args["id"], "idatractivo");
    }
    //Eliminar el registro del Atractivo
    $respuesta = dbDelete("atractivos", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Eliminar un gastronomico
$app->delete("/gastronomia/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    //Eliminar las imagenes del directorio y los registros de la tabla atractivo_imgs
    $xSQL = "SELECT id, imagen FROM gastronomia_imgs WHERE idgastronomia = " . $args["id"];
    $imagenes = dbGet($xSQL);
    if ($imagenes->data["count"] > 0) {
        for ($i = 0; $i < count($imagenes->data["registros"]); $i++) {
            if ($imagenes->data["registros"][$i]->imagen <> "default.jpg") {
                $fileX = $imagenes->data["registros"][$i]->imagen;
                @unlink($this->get("upload_directory_gastronomia") . "\\$fileX");
            }
        }
        dbDelete("gastronomia_imgs", $args["id"], "idgastronomia");
    }
    //Eliminar el registro del Atractivo
    $respuesta = dbDelete("gastronomia", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


//Eliminar una imagen de un Atractivo
$app->delete("/atractivo/imagen/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $archivo = dbGet("SELECT imagen FROM atractivo_imgs WHERE id = " . $args["id"]);
    if ($archivo->err == false && $archivo->data["count"] > 0) {
        $fileX = $archivo->data["registros"][0]->imagen;
        @unlink($this->get("upload_directory_atractivo") . "\\$fileX");
    }
    $respuesta = dbDelete("atractivo_imgs", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

//Eliminar una imagen de un gastronomico
$app->delete("/gastronomia/imagen/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
    $archivo = dbGet("SELECT imagen FROM gastronomia_imgs WHERE id = " . $args["id"]);
    if ($archivo->err == false && $archivo->data["count"] > 0) {
        $fileX = $archivo->data["registros"][0]->imagen;
        @unlink($this->get("upload_directory_gastronomia") . "\\$fileX");
    }
    $respuesta = dbDelete("gastronomia_imgs", $args["id"]);
    return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
