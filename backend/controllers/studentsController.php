<?php
require_once("./models/students.php");

function handleGet($conn) {
    if (isset($_GET['id'])) {
        $result = getStudentById($conn, $_GET['id']);
        echo json_encode($result->fetch_assoc());
    } else {
        $result = getAllStudents($conn);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}

function handlePost($conn) {
    try{
        $input = json_decode(file_get_contents("php://input"), true);        
        if (createStudent($conn, $input['fullname'], $input['email'], $input['age'])) {
            echo json_encode(["message" => "Estudiante agregado correctamente"]);
        }
    }catch(Exception $e){
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}

function handlePut($conn) {
    try{
        $input = json_decode(file_get_contents("php://input"), true);
        if (updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age'])) {
            echo json_encode(["message" => "Actualizado correctamente"]);
        }
    }catch(Exception $e){
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}

function handleDelete($conn) {
    try{
        $input = json_decode(file_get_contents("php://input"), true);
        if (deleteStudent($conn, $input['id'])) {
            echo json_encode(["message" => "Eliminado correctamente"]);
        } 
    }catch(Exception $e){
        http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
        echo json_encode(["error" => $e->getMessage()]);
        return;
    }
}
?>