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
        if (createSubject($conn, $input['name'], $input['description'], $input['professor_name'], $input['year'])) {
            echo json_encode(["message" => "Materia agregada correctamente"]);
        }
    }catch(Exception $e){
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}
function handlePut($conn) {
    try {
        $input = json_decode(file_get_contents("php://input"), true);
        if (updateSubject($conn, $input['subject_id'], $input['name'], $input['description'], $input['professor_name'], $input['year'])) {
            echo json_encode(["message" => "Actualizado correctamente"]);
        }
    } catch (Exception $e) {
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}
function handleDelete($conn) {
    try{
        $input = json_decode(file_get_contents("php://input"), true);
        if (deleteSubject($conn, $input['subject_id'])) {
            echo json_encode(["message" => "Eliminado correctamente"]);
        }
    }catch(Exception $e){
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}
