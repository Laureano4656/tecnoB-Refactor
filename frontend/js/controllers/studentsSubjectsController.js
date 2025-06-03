import { studentsSubjectsAPI } from '../api/studentsSubjectsAPI.js'

import { subjectsAPI } from '../api/subjectsAPI.js'

function getFormData() {
	const urlParams = new URLSearchParams(window.location.search)
	const studentId = parseInt(urlParams.get('studentId'))
	return {
		id: parseInt(document.getElementById('studentSubjectId').value),
		subject_id: parseInt(document.getElementById('materia').value),
		student_id: parseInt(studentId),
		state: document.getElementById('state').value,
		nota: document.getElementById('grade').value,
	}
}

function clearForm() {
	document.getElementById('studentSubjectForm').reset()
	document.getElementById('studentSubjectId').value = ''
	document.getElementById('materia').disabled = false
}

async function loadSubjects() {
	try {
		const subjects = await subjectsAPI.fetchAll()
		renderSubjectSelect(subjects)
	} catch (err) {
		alert('Error cargando materias')
		console.error(err.message)
	}
}
function createSubjectOption(subject) {
	const option = document.createElement('option')
	option.value = subject.subject_id
	option.textContent = subject.name
	return option
}
function renderSubjectSelect(subjects) {
	const subjectSelect = document.getElementById('materia')
	subjectSelect.replaceChildren()
	const defaultOption = document.createElement('option')
	defaultOption.value = ''
	defaultOption.textContent = 'Seleccionar materia'
	defaultOption.selected = true
	defaultOption.disabled = true
	subjectSelect.appendChild(defaultOption)

	subjects.forEach(subject => {
		const option = createSubjectOption(subject)
		subjectSelect.appendChild(option)
	})
}

function setupFormHandler() {
	const studentSubjectForm = document.getElementById('studentSubjectForm')
	studentSubjectForm.addEventListener('submit', async e => {
		e.preventDefault()
		const StudentSubject = getFormData()

		try {
			if (StudentSubject.id) {
				await studentsSubjectsAPI.update(StudentSubject)
			} else {
				await studentsSubjectsAPI.create(StudentSubject)
			}
			clearForm()
			loadStudentSubjects()
		} catch (err) {
			alert(`Error guardando materia del estudiante: ${err}`)
		}
	})
}

async function loadStudentSubjects() {
	const urlParams = new URLSearchParams(window.location.search)
	const studentId = urlParams.get('studentId')
	console.log('id: ' + studentId)
	try {
		const StudentSubjects = await studentsSubjectsAPI.fetchByStudentId(studentId)
		renderStudentSubjectTable(StudentSubjects)
		document.getElementById(
			'titulo',
		).textContent = `Materias de ${StudentSubjects[0].fullname}`
	} catch (err) {
		alert('Error cargando materias del estudiante')
		console.error(err.message)
	}
}

function createCell(text) {
	const td = document.createElement('td')
	td.textContent = text
	return td
}

function fillForm(studentSubject) {
	document.getElementById('studentSubjectId').value = studentSubject.id
	document.querySelector(
		`option[value="${studentSubject.subject_id}"]`,
	).selected = true
	document.getElementById('materia').disabled = true
	document.getElementById('state').value = studentSubject.state
	document.getElementById('grade').value = studentSubject.grade
}

function createActionsCell(studentSubject) {
	const tdActions = document.createElement('td')
	const editBtn = document.createElement('a')
	editBtn.className = 'tooltip-container'
	const editIcon = document.createElement('span')
	editIcon.className = 'material-icons'
	editIcon.textContent = 'edit'
	const editToolTip = document.createElement('div')
	editToolTip.className = 'tooltip'
	editToolTip.textContent = 'Editar'
	editBtn.appendChild(editToolTip)
	editBtn.appendChild(editIcon)
	editBtn.addEventListener('click', () => fillForm(studentSubject))

	const deleteBtn = document.createElement('a')
	deleteBtn.classList.add(
		'tooltip-container',
		'w3-margin-left',
		'w3-margin-right',
	)
	const deleteIcon = document.createElement('span')
	deleteIcon.className = 'material-icons'
	deleteIcon.textContent = 'delete'
	const deleteToolTip = document.createElement('div')
	deleteToolTip.className = 'tooltip'
	deleteToolTip.textContent = 'Eliminar'
	deleteBtn.appendChild(deleteToolTip)
	deleteBtn.appendChild(deleteIcon)
	deleteBtn.addEventListener('click', () =>
		deleteStudentSubject(studentSubject.id),
	)

	tdActions.appendChild(editBtn)
	tdActions.appendChild(deleteBtn)

	return tdActions
}

function renderStudentSubjectTable(studentSubjects) {
	const StudentSubjectTableBody = document.getElementById(
		'studentSubjectTableBody',
	)
	StudentSubjectTableBody.replaceChildren()
	//acá innerHTML es seguro a XSS porque no hay entrada de usuario
	//igual no lo uso.
	//subjectTableBody.innerHTML = "";

	studentSubjects.forEach(StudentSubject => {
		const tr = document.createElement('tr')
		tr.appendChild(createCell(StudentSubject.name))
		tr.appendChild(createCell(StudentSubject.grade))
		tr.appendChild(createCell(StudentSubject.state))
		tr.appendChild(createActionsCell(StudentSubject))
		StudentSubjectTableBody.appendChild(tr)
	})
}
async function deleteStudentSubject(id) {
	if (!confirm('¿Seguro que querés borrar esta materia de este alumno?')) return

	try {
		const response = await studentsSubjectsAPI.remove(id)
		loadSubjects()
	} catch (err) {
		console.error(err)
		alert('Error al borrar la materia del estudiante')
	}
}
document.addEventListener('DOMContentLoaded', () => {
	setupFormHandler()
	loadStudentSubjects()
	loadSubjects()
})
