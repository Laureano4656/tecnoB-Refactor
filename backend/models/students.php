<?php
function getAllStudents($conn) {
    $sql = "SELECT * FROM students";
    return $conn->query($sql);
}

function getStudentById($conn, $id) {
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

function createStudent($conn, $fullname, $email, $age) {
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age);
    return ["state"=>$stmt->execute(),"errno"=>$stmt->errno];
    // try {
    //     if ($stmt->execute()) {
    //         return true;
    //     } else {
    //         throw new Exception("Error al agregar el estudiante: " . $stmt->error, $stmt->errno);
    //     }
    // } catch (Exception $e) {
    //     if ($e->getCode() == 1062) { // Duplicate entry error code
    //         throw new Exception("Ya existe un estudiante con el mismo email", 409);
    //     } else {
    //         throw $e;
    //     }
    // }    
}

function updateStudent($conn, $id, $fullname, $email, $age) {
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    return ["state"=>$stmt->execute(),"errno"=>$stmt->errno];
    // try{
    //     if ($stmt->execute()) {
    //         return true;
    //     } else {
    //         throw new Exception("Error al actualizar el estudiante: " . $stmt->error, $stmt->errno);
    //     }
    // } catch (Exception $e) {
    //     if ($e->getCode() == 1062) { // Duplicate entry error code
    //         throw new Exception("Ya existe un estudiante con el mismo email", 409);
    //     } else {
    //         throw $e;
    //     }
    // }    
}

function deleteStudent($conn, $id) {
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return ["state"=>$stmt->execute(), "errno"=>$stmt->errno];

}
?>