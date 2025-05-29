<?php
function getAllSubjects($conn){
    $sql = "SELECT * FROM subjects";
    return $conn->query($sql);
}
function getSubjectById($conn, $id){
    $sql = "SELECT * FROM subjects WHERE subject_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}
function createSubject($conn, $name, $description, $professor_name, $year){
    $sql = "INSERT INTO subjects (name, description,professor_name,year) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $description, $professor_name, $year);
    try {
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al agregar la materia: " . $stmt->error, $stmt->errno);
        }
    } catch (Exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code
            throw new Exception("Ya existe una materia con el mismo nombre", 409);
        } else {
            throw $e;
        }
    }
}
function updateSubject($conn, $id, $name, $description, $professor_name, $year){
    $sql = "UPDATE subjects SET name = ?, description = ?, professor_name = ?, year = ? WHERE subject_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $name, $description, $professor_name, $year, $id);
    try {
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al actualizar la materia: " . $stmt->error, $stmt->errno);
        }
    } catch (Exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code
            throw new Exception("Ya existe una materia con el mismo nombre", 409);
        } else {
            throw $e;
        }
    }
}

function deleteSubject($conn, $id){
    $sql = "DELETE FROM subjects WHERE subject_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    try{
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al eliminar la materia: " . $stmt->error, $stmt->errno);
        }
    } catch (Exception $e) {
        if ($e->getCode() == 1451) { // Foreign key constraint error code
            throw new Exception("No se puede eliminar la materia porque está siendo utilizada por un estudiante", 409);
        } else {
            throw $e;
        }
    }
}

?>