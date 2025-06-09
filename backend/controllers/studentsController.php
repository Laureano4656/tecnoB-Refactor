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
        $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
        if ($result["state"]) {
            echo json_encode(["message" => "Estudiante agregado correctamente"]);
        }else{
            throw new Exception("Error al agregar el estudiante", $result['errno']);
        }
    }catch(Exception $e){
        if ($e->getCode() == 1062) { // Duplicate entry error code
            http_response_code(409); // Conflict
            echo json_encode(["error" => "Ya existe un estudiante con el mismo email"]);
        } else {
            http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
            echo json_encode(["error" => $e->getMessage()]);
        }        
        return;
    }
}

function handlePut($conn) {
    try{
        $input = json_decode(file_get_contents("php://input"), true);
        $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
        if ($result["state"]) {
            echo json_encode(["message" => "Actualizado correctamente"]);
        }else{
            throw new Exception("Error al actualizar el estudiante", $result["errno"]);
        }
    }catch(Exception $e){
        if ($e->getCode() == 1062) { // Duplicate entry error code
            http_response_code(409); // Conflict
            echo json_encode(["error" => "Ya existe un estudiante con el mismo email"]);
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
        $result = deleteStudent($conn, $input['id']);
        if ($result["state"]) {
            echo json_encode(["message" => "Eliminado correctamente"]);
        } else {
            throw new Exception("Error al eliminar el estudiante", $result["errno"]);
        }
    }catch(Exception $e){
        if ($e->getCode() == 1451) { 
            http_response_code(409); 
            echo json_encode(["error" => "No se puede eliminar el estudiante porque tiene materias asociadas"]);
        } else {
            http_response_code($e->getCode() ?: 500); // Use the exception code or default to 500
            echo json_encode(["error" => $e->getMessage()]);
        }
        return;
    }
}
?>