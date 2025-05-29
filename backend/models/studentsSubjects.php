<?php
function getStudentSubjects($conn, $id)
{
  $sql = 'SELECT students_subjects.id,subjects.subject_id,subjects.name,students.fullname,students.id AS student_id,state,grade 
     FROM students_subjects
     INNER JOIN students ON students.id = students_subjects.student_id
     INNER JOIN subjects ON subjects.subject_id = students_subjects.subject_id
     WHERE students_subjects.student_id = ?';

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result();
}
function createStudentSubject($conn, $student_id, $subject_id, $state, $nota)
{
  $sql = "INSERT INTO students_subjects (student_id, subject_id,state,grade) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iisi", $student_id, $subject_id, $state, $nota);
  try {
    
    if ($stmt->execute())
      return true;
    else
      throw new Exception("Error al agregar la materia" . $stmt->error, $stmt->errno);
  } catch (Exception $e) {    
    if ($e->getCode() == 1062) { // Duplicate entry error code
      throw new Exception("Ya existe una materia con el mismo nombre para este estudiante",409);
    } else {
      throw $e;
    }
  }
}

function updateStudentSubject($conn, $id, $state, $nota)
{
  $sql = "UPDATE students_subjects SET state = ?, grade = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $state, $nota, $id);
  return $stmt->execute();
}

function deleteStudentSubject($conn, $id)
{
  $sql = "DELETE FROM students_subjects WHERE student_subject_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  return $stmt->execute();
}
