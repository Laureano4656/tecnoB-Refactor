<?php
require_once("./models/subjects.php");

function handleGet($conn)
{
    if (isset($_GET['subject_id'])) {
        $result = getSubjectById($conn, $_GET['subject_id']);
        echo json_encode($result->fetch_assoc());
    } else {
        $result = getAllSubjects($conn);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}
function handlePost($conn){
    $input = json_decode(file_get_contents("php://input"), true);
    if (createSubject($conn, $input['name'], $input['description'], $input['professor_name'], $input['year'])) {
        echo json_encode(["message" => "Materia agregada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
}
function handlePut($conn) {
  $input = json_decode(file_get_contents("php://input"), true);
    if (updateSubject($conn, $input['subject_id'], $input['name'], $input['description'], $input['professor_name'], $input['year'])) {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}
function handleDelete($conn) {
    $input = json_decode(file_get_contents("php://input"), true);
    if (deleteSubject($conn, $input['subject_id'])) {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
