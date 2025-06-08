<?php
/**
*    File        : backend/routes/studentsSubjectsRoutes.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsSubjectsController.php");

routeRequest($conn, [
    'POST' => function($conn) {
        $input = json_decode(file_get_contents("php://input"), true); /**recibo datos */
        $student_id = $input['student_id']; /**recibo id de materia y estudiante */
        $subject_id = $input['subject_id'];

        // valido relacion duplicada
        $stmt = $conn->prepare("SELECT COUNT(*) FROM students_subjects WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $student_id, $subject_id); /**consulta segura */
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($count > 0) { /**si la relacion ya existe tiro error */
            http_response_code(400);
            echo json_encode(["error" => "Esa relación ya existe"]);
            return;
        }

        handlePost($conn);
    }
]);
