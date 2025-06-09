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
    try{
        $input = json_decode(file_get_contents("php://input"), true);
        $result = createSubject($conn, $input['name'], $input['description'], $input['professor_name'], $input['year']);
        if ($result["state"]) {
            echo json_encode(["message" => "Materia agregada correctamente"]);
        }else {
            throw new Exception("Error al agregar la materia", $result['errno']);
        }
    }catch(Exception $e){
        if ($e->getCode() == 1062) { // Duplicate entry error code
            http_response_code(409); // Conflict
            echo json_encode(["error" => "Ya existe una materia con el mismo nombre"]);
        } else {
            http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
            echo json_encode(["error" => $e->getMessage()]);
        }
        return;
    }
}
function handlePut($conn) {
    try {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = updateSubject($conn, $input['subject_id'], $input['name'], $input['description'], $input['professor_name'], $input['year']);
        if ($result["state"]) {
            echo json_encode(["message" => "Actualizado correctamente"]);
        } else {
            throw new Exception("Error al actualizar la materia", $result["errno"]);
        }
    } catch (Exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code
            http_response_code(409); // Conflict
            echo json_encode(["error" => "Ya existe una materia con el mismo nombre"]);
        } else {
            http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
            echo json_encode(["error" => $e->getMessage()]);
        }
        return;
    }
}
function handleDelete($conn) {
    try{
        $input = json_decode(file_get_contents("php://input"), true);
        $result = deleteSubject($conn, $input['id']);
        if ($result["state"]) {
            echo json_encode(["message" => "Eliminado correctamente"]);
        }else{ 
            throw new Exception("Error al eliminar la materia", $result["errno"]);
        }
    }catch(Exception $e){
        if ($e->getCode() == 1451) { 
            http_response_code(409); // Conflict
            echo json_encode(["error" => "No se puede eliminar la materia porque está siendo utilizada por un estudiante"]);
        } else {
            http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
            echo json_encode(["error" => $e->getMessage()]);
        }
        return;
    }
}
