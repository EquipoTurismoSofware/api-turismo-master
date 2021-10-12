
<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Obtener usuario
    $app->get("/usuarios/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
        $xSQL = "SELECT * FROM usuarios_app WHERE id = ". $args["id"] ;
        $respuesta = dbGet($xSQL);
        if($respuesta->data["count"]!=0){
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
        else{
            $resperr = new stdClass();
            $resperr->err = true;
            $resperr->errMsg = "No se encontro el Usuario.";
            return $response
                ->withStatus(409) //Conflicto
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    });
    $app->get("/usuarios", function (Request $request, Response $response, array $args) {
        $parsedBody = $request->getParsedBody();
        $id =$parsedBody["id"];
        if($parsedBody!=null){
        $xSQL = "SELECT * FROM usuarios_app WHERE id=".$id;
        }
        else{
        $xSQL = "SELECT * FROM usuarios_app";
        }
        $respuesta = dbGet($xSQL);
        if($respuesta->data["count"]!=0){
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
        else{
            $resperr = new stdClass();
            $resperr->err = true;
            $resperr->errMsg = "No se encontro el Usuario.";
            return $response
                ->withStatus(409) //Conflicto
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    });

// Dar de alta Usuario
$app->post("/usuarios", function (Request $request, Response $response, array $args) {
        //Hay que asegurarse de que no se creen 2 usuarios con el mismo mail!!!
         $parsedBody = $request->getParsedBody();
         $email= $parsedBody["emailUser"];
       $xSQL = "SELECT * FROM usuarios_app WHERE emailUser = '$email'";

        $res = dbGet($xSQL)->data["count"];

        if($res==0) {
            $data = array(
                "nombreUser" =>$parsedBody["nombreUser"],
                "apellidoUser" =>$parsedBody["apellidoUser"],
                "emailUser" =>$parsedBody["emailUser"],
                "ingreso" => date("Y-m-d"),
            );
            $respuesta = dbPostWithData("usuarios_app", $data);
            $respuesta->ok = "Registro correcto";
           return $response
               ->withStatus(200) //Ok
               ->withHeader("Content-Type", "application/json")
               ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        } else {
            $resperr = new stdClass();
            $resperr->err = true;
            $resperr->errMsg = "El usuario ya se encuentra registrado";
            // $resperr->errMsgs = $validar->errors();
            return $response
                ->withStatus(409) //Conflicto
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($resperr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    });

    $app->patch("/usuarios/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
        $respuesta= new stdClass;
        $parsedBody = $request->getParsedBody();
            $data = array(
                "nombreUser" =>$parsedBody["nombreUser"],
                "apellidoUser" =>$parsedBody["apellidoUser"],
                "emailUser" =>$parsedBody["emailUser"],
            );
            if($data["nombreUser"]!=null){
            $respuesta = dbPatch2("usuarios_app", $args["id"], $data);     
            }
            else{
                $respuesta->err=true;
                $respuesta->errMsg="Error con el objeto, verifique los campos";
            }       
            return $response
            ->withStatus(200) //Ok
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));  
        

    });
    ?>