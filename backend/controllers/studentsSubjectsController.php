<?php
require_once("./models/studentsSubjects.php");

function handleGet($conn)
{
    if (isset($_GET['id'])) {
        $result = getStudentSubjects($conn, $_GET['id']);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "ID not provided"]);
    }
}

function handlePost($conn)
{
    try {
        $input = json_decode(file_get_contents("php://input"), true); 
        
        if (createStudentSubject($conn, $input['student_id'], $input['subject_id'], $input['state'], $input['nota'])) {
            echo json_encode(["message" => "Materia para estudiante agregado correctamente"]);
        } 
    } catch (Exception $e) {              
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}

function handlePut($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (updateStudentSubject($conn, $input['id'], $input['state'], $input['nota'])) {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (deleteStudentSubject($conn, $input['id'])) {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
