<?php
function getStudentSubjects($conn, $id)
{
  $sql = 'SELECT student_subject_id,subjects.subject_id,subjects.name,students.fullname,students.id AS student_id,state,nota 
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
  $sql = "INSERT INTO students_subjects (student_id, subject_id,state,nota) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iisi", $student_id, $subject_id, $state, $nota);
  return $stmt->execute();
}

function updateStudentSubject($conn, $id, $state, $nota)
{
  $sql = "UPDATE students_subjects SET state = ?, nota = ? WHERE student_subject_id = ?";
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


?>
