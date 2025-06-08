<?php
/**
*    File        : backend/routes/subjectsRoutes.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/subjectsController.php");

routeRequest($conn, [
    'POST' => function($conn) {
        $input = json_decode(file_get_contents("php://input"), true); /**recibo datos del formulario (formato JSON) */

        if (empty($input['name'])) { /**valido que venga un nombre */
            http_response_code(400);
            echo json_encode(["error" => "Falta el nombre de la materia"]);
            return;
        }

        // valido nombre duplicado
        $name = $input['name'];
        $stmt = $conn->prepare("SELECT COUNT(*) FROM subjects WHERE name = ?");
        $stmt->bind_param("s", $name); /**consulta segura con prepare y bind param */
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch(); 
        if ($count > 0) {
            http_response_code(400); /**tiro error si la materia ya existe */
            echo json_encode(["error" => "La materia ya existe"]);
            return;
        }

        handlePost($conn);
    } , 
    'DELETE' => function($conn) {
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input['id'];

        $stmt = $conn->prepare("SELECT COUNT(*) FROM students_subjects WHERE subject_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        if ($count > 0) {
            http_response_code(400);
            echo json_encode(["error" => "No se puede eliminar el estudiante porque tiene materias asignadas"]);
            return;
        }

        handleDelete($conn);
    }



]);