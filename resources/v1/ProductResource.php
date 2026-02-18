<?php
require_once '../core/loginCheck.php';
require_once '../config/database.php';
require_once '../models/product.php';

class ProductResource
{
    private $db;
    private $product;

    public function __construct()
    {
        //LoginCheck::check();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    // GET /api/v1/products
    public function index()
    {
        LoginCheck::check();
        header("Content-Type: application/json");

        $stmt = $this->product->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $products_arr = array();
            $products_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $product_item = array(
                    "id" => $id,
                    "name" => $name,
                    "price" => $price,
                    "created_at" => $created_at
                );
                array_push($products_arr["records"], $product_item);
            }

            http_response_code(200);
            echo json_encode($products_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array()));
        }
    }

    // GET /api/v1/products/{id}
    public function show($id)
    {
        LoginCheck::check();
        header("Content-Type: application/json");
        $this->product->id = $id;
        if ($this->product->readOne()) {
            $product_item = array(
                "id" => $this->product->id,
                "name" => $this->product->name,
                "price" => $this->product->price,
                "created_at" => $this->product->created_at
            );
            http_response_code(200);
            echo json_encode($product_item);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Product not found."));
        }
    }

    // POST /api/v1/products
    public function store()
    {
        LoginCheck::check();
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"));
        $this->product->name = $data->name;
        $this->product->price = $data->price;
        if ($this->product->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Product was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create product."));
        }
    }

    // PUT /api/v1/products/{id}
    public function update($id)
    {
        LoginCheck::check();
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"));
        $this->product->id = $id;
        $this->product->name = $data->name;
        $this->product->price = $data->price;
        if ($this->product->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Product was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update product."));
        }
    }

    // DELETE /api/v1/products/{id}
    public function delete($id)
    {
        LoginCheck::check();
        header("Content-Type: application/json");
        $this->product->id = $id;
        if ($this->product->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Product was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete product."));
        }
    }
}
?>
