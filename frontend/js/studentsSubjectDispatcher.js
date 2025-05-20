//frontDispatcher_2.0
const API_URL = '../../backend/server.php/studentsSubjects';

document.addEventListener('DOMContentLoaded', () => 
{
    // Parse student_id from URL
    const urlParams = new URLSearchParams(window.location.search);
    const studentId = urlParams.get('studentId');
    console.log(studentId);
    const studentSubjectForm = document.getElementById('studentSubjectForm');
    const studentsSubjectsTableBody = document.getElementById('studentSubjectTableBody');
    const stateInput = document.getElementById('state');
    const notaInput = document.getElementById('nota');
    const materiaInput = document.getElementById('materia');
    const studentSubjectIdInput = document.getElementById('studentSubjectId');

    // Leer todos los estudiantes al cargar
    fetchStudentsSubjects();
    fetchSubjects()
    // Formulario: Crear o actualizar estudiante
    studentSubjectForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = {
            state: stateInput.value,
            nota: notaInput.value,
            subject_id: materiaInput.value,
            student_id: studentId,            
        };

        const id = studentSubjectIdInput.value;
        const method = id ? 'PUT' : 'POST';
        if (id) formData.id = id;

        try 
        {
            const response = await fetch(API_URL, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            });

            if (response.ok) {
                studentSubjectForm.reset();
                studentSubjectIdInput.value = '';
                await fetchStudentsSubjects();
            } else {
                alert("Error al guardar");
            }
        } catch (err) {
            console.error(err);
        }
    });

    // Obtener estudiantes y renderizar tabla
    async function fetchStudentsSubjects() 
    {
        try 
        {
            const res = await fetch(`${API_URL}?id=${studentId}`);
            const studentsSubjects = await res.json();
            console.log(studentsSubjects);
            //Limpiar tabla de forma segura.
            studentsSubjectsTableBody.replaceChildren();
            //acá innerHTML es seguro a XSS porque no hay entrada de usuario
            //igual no lo uso.
            //studentsSubjectsTableBody.innerHTML = "";
            document.getElementById('titulo').textContent = `Materias de ${studentsSubjects[0].fullname}`;
            console.log(studentsSubjectsTableBody)
            studentsSubjects.forEach(studentSubject => {
                const tr = document.createElement('tr');

                const tdName = document.createElement('td');
                tdName.textContent = studentSubject.name;

                const tdNota = document.createElement('td');
                tdNota.textContent = studentSubject.nota;

                const tdState = document.createElement('td');
                tdState.textContent = studentSubject.state;

                const tdActions = document.createElement('td');
                const editBtn = document.createElement('button');
                editBtn.textContent = 'Editar';
                editBtn.classList.add('w3-button', 'w3-blue', 'w3-small', 'w3-margin-right');
                editBtn.onclick = () => {
                    stateInput.value = studentSubject.state;
                    notaInput.value = studentSubject.nota;
                    document.querySelector(`option[value="${studentSubject.subject_id}"]`).selected = true;
                    materiaInput.disabled = true;
                    studentSubjectIdInput.value = studentSubject.student_subject_id;
                };

                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Borrar';
                deleteBtn.classList.add('w3-button', 'w3-red', 'w3-small','w3-margin-right');
                deleteBtn.onclick = () => deleteStudentSubject(studentSubject.student_subject_id);
                tdActions.appendChild(editBtn);
                tdActions.appendChild(deleteBtn);
                
                tr.appendChild(tdName);
                tr.appendChild(tdNota);
                tr.appendChild(tdState);
                tr.appendChild(tdActions);
                studentsSubjectsTableBody.appendChild(tr);
            });
        } catch (err) {
            console.error("Error al obtener estudiantes:", err);
        }
    }
    async function fetchSubjects(){
        const res = await fetch('../../backend/server.php/subjects');
        const subjects = await res.json();
        subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject.subject_id;
            option.textContent = subject.name;
            materiaInput.appendChild(option);
        });
    }
    // Eliminar estudiante
    async function deleteStudentSubject(id) 
    {
        if (!confirm("¿Seguro que querés borrar esta materia de este estudiante?")) return;

        try 
        {
            const response = await fetch(API_URL, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }),
            });

            if (response.ok) {
                await fetchStudentsSubjects();
            } else {
                alert("Error al borrar");
            }
        } catch (err) {
            console.error(err);
        }
    }
});
