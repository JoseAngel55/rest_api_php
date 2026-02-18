<?php

class Login
{
    private $conn;
    private $table = "api_tokens";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createToken($user_id)
    {
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $query = "INSERT INTO " . $this->table . "
                  (user_id, token, expires_at)
                  VALUES (:user_id, :token, :expires_at)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expires_at", $expires_at);

        if ($stmt->execute()) {
            return [
                "access_token" => $token,
                "expires_at" => $expires_at
            ];
        }

        return false;
    }

    public function validateToken($token)
    {
        $query = "SELECT id FROM " . $this->table . "
                  WHERE token = :token
                  AND expires_at > NOW()
                  AND revoked = FALSE
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
