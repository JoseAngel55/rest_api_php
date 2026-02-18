<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Api_user.php';
require_once __DIR__ . '/../../models/login.php';

class LoginResource
{
    private $db;
    private $apiUser;
    private $auth;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->apiUser = new ApiUser($this->db);
        $this->auth = new Login($this->db);
    }

    public function login()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(["message" => "Credenciales incompletas"]);
            return;
        }

        $user = $this->apiUser->findByUsername($data->username);

        if (!$user || !password_verify($data->password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(["message" => "Sin acceso"]);
            return;
        }

        $token = $this->auth->createToken($user['id']);

        http_response_code(200);
        echo json_encode($token);
    }
}
