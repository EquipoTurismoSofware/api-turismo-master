<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;

     //[GET]
    //Todas los Newsletters
    $app->get("/newsletter/all", function (Request $request, Response $response, array $args) {
        $xSQL = "SELECT * FROM newsletter";
        $respuesta = dbGet($xSQL);
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

    //Todo los Newsletters activos
    $app->get("/newsletter/activos", function (Request $request, Response $response, array $args) {
        $xSQL = "SELECT * FROM newsletter WHERE activo = 1";
        $respuesta = dbGet($xSQL);
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

    //Actualizar Newsletter
    $app->patch("/newsletter/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
        $reglas = array(
            "email" => array(
                "min" => 5,
                "max" => 100,
                "tag" => "Email"
            ),
            "activo" => array(
                "tag" => "Activo"
            )
        );
                
        $parsedBody = $request->getParsedBody();
        $validar = new Validate();
        if($validar->validar($parsedBody, $reglas)) {
            $data = array(
                "email" => $parsedBody["email"],
                "activo" => $parsedBody["activo"]
            );
            $respuesta = dbPatchWithData("newsletter", $args["id"], $data);       
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

    
    //Crear Nuevo Newsletter
    $app->post("/newsletter", function (Request $request, Response $response, array $args) {
        $reglas = array(
            "email" => array(
                "min" => 5,
                "max" => 100,
                "tag" => "Email"
            )
        );
                
        $validar = new Validate();
        $parsedBody = $request->getParsedBody();
        if($validar->validar($parsedBody, $reglas)) {
            $data = array(
                "email" => $parsedBody["email"],
                "activo" => 1
            );
            $respuesta = dbPostWithData("newsletter", $data);    
            
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

    //Eliminar un Usuario
    $app->delete("/newsletter/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
        $data = array(
            "activo" => 0
        );
        $respuesta = dbPatchWithData("newsletter", $args["id"], $data);
        return $response
            ->withStatus(200) //Ok
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

   //[GET]
    //Todas los Newsletters
    $app->get("/exportmails", function (Request $request, Response $response, array $args) {
        $xSQL = "SELECT email FROM newsletter WHERE activo = 1";
        $xSQL .= " ORDER BY email";
        $respuesta = dbGet($xSQL);
        return $response
        ->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    });  
    


?>