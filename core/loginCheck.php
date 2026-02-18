<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/login.php';

class LoginCheck
{
    public static function check()
    {
        header("Content-Type: application/json");

        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token requerido"]);
            exit;
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);

        $database = new Database();
        $db = $database->getConnection();
        $auth = new Login($db);

        if (!$auth->validateToken($token)) {
            http_response_code(401);
            echo json_encode(["message" => "Token inválido o expirado"]);
            exit;
        }
    }
}
