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
    $respuesta = dbPostWithData("censista", $parsedBody);
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
    $respuesta = dbPatchWithData("censista", $args["id"], $parsedBody);
    return $response
        ->withStatus(200) //Ok
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respuesta, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});

?>