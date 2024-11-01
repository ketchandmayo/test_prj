<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Helpers/response.php';

class UserController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }

    public function processRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $path[0] =strtok($path[0], '?');
        $data = json_decode(file_get_contents("php://input"), true);

        switch ($method) {
            case 'GET':
                if ($path[0] != 'get') {
                    echo invalidRequest();
                    break;
                }

                $role = $_GET['role'] ?? null;
                $id = $path[1] ?? null;

                if (
                    (!is_string($role) && isset($role))||
                    (!is_numeric($id) && isset($id))
                ){
                    echo error("invalid parameters");
                    break;
                }

                if(empty($id)) {
                    if($data = $this->user->readAll($role))
                        echo response(true, ['users' => $data]);
                    else
                        echo error("users not found");
                }
                else {
                    if ($data = $this->user->readOne($id))
                        echo response(true, ['users' => $data]);
                    else
                        echo error("user with ID $id not found");
                }

                break;




            case 'POST':
                if ($path[0] != 'create') {
                    echo invalidRequest();
                    break;
                }

                if (
                    empty($data['full_name']) ||
                    empty($data['role']) ||
                    (empty($data['efficiency']) && $data['efficiency'] != 0)
                )
                {
                    echo error("missing parameters");
                    break;
                }

                if(
                    !is_string($data['full_name']) ||
                    !is_string($data['role']) ||
                    !is_numeric($data['efficiency']) ||

                    $data['efficiency'] < 0 ||
                    $data['efficiency'] > 100 ||

                    strlen($data['full_name']) > 255 ||
                    strlen($data['role']) > 255
                )
                {
                    echo error("invalid parameters");
                    break;
                }

                if ($id = (int)$this->user->create($data['full_name'], $data['role'], $data['efficiency'])) {
                    echo response(true, ['id' => $id]);
                } else {
                    echo error("user not created");
                }
                break;

            case 'PATCH':
                if ($path[0] != 'update') {
                    echo invalidRequest();
                    break;
                }

                $id = $path[1] ?? null;

                if(
                    (isset($data['full_name']) && !is_string($data['full_name'])) ||
                    (isset($data['role']) && !is_string($data['role'])) ||
                    (isset($data['efficiency']) && !is_numeric($data['efficiency'])) ||

                    (isset($data['efficiency']) && $data['efficiency'] < 0) ||
                    (isset($data['efficiency']) && $data['efficiency'] > 100) ||

                    (isset($data['full_name']) && strlen($data['full_name']) > 255) ||
                    (isset($data['role']) && strlen($data['role']) > 255) ||
                    !is_numeric($id)
                )
                {
                    echo error("invalid parameters");
                    break;
                }

                if ($data = $this->user->update($id, $data)) {
                    echo response(true, $data);
                } else {
                    echo error("user not patched");
                }
                break;

            case 'DELETE':
                if ($path[0] != 'delete') {
                    echo invalidRequest();
                    break;
                }

                $id = $path[1] ?? null;
                if(
                    !is_numeric($id) && isset($id)
                )
                {
                    echo error("invalid parameters");
                    break;
                }

                if(empty($id)) {
                    if($this->user->deleteAll())
                        echo response(true);
                    else
                        echo error("users not found");
                }
                else {
                    if ($data = $this->user->deleteOne($id))
                        echo response(true, ['users' => $data]);
                    else
                        echo error("user with ID $id not found");
                }
                break;

            default:
                echo error("invalid request");
                break;
        }
    }
}
