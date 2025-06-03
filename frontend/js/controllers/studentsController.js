import { studentsAPI } from '../api/studentsAPI.js'

document.addEventListener('DOMContentLoaded', () => {
	loadStudents()
	setupFormHandler()
})

function setupFormHandler() {
	const form = document.getElementById('studentForm')
	form.addEventListener('submit', async e => {
		e.preventDefault()
		const student = getFormData()

		try {
			if (student.id) {
				await studentsAPI.update(student)
			} else {
				await studentsAPI.create(student)
			}
			clearForm()
			loadStudents()
		} catch (err) {
			alert('Error al guardar el estudiante: ' + err)
			console.error(err)
		}
	})
}

function getFormData() {
	return {
		id: document.getElementById('studentId').value.trim(),
		fullname: document.getElementById('fullname').value.trim(),
		email: document.getElementById('email').value.trim(),
		age: parseInt(document.getElementById('age').value.trim(), 10),
	}
}

function clearForm() {
	document.getElementById('studentForm').reset()
	document.getElementById('studentId').value = ''
}

async function loadStudents() {
	try {
		const students = await studentsAPI.fetchAll()
		renderStudentTable(students)
	} catch (err) {
		console.error('Error cargando estudiantes:', err)
	}
}

function renderStudentTable(students) {
	const tbody = document.getElementById('studentTableBody')
	tbody.replaceChildren()

	students.forEach(student => {
		const tr = document.createElement('tr')

		tr.appendChild(createCell(student.fullname))
		tr.appendChild(createCell(student.email))
		tr.appendChild(createCell(student.age.toString()))
		tr.appendChild(createActionsCell(student))

		tbody.appendChild(tr)
	})
}

function createCell(text) {
	const td = document.createElement('td')
	td.textContent = text
	return td
}

function createActionsCell(student) {
	const td = document.createElement('td')
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

	editBtn.addEventListener('click', () => fillForm(student))

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

	deleteBtn.addEventListener('click', () => confirmDelete(student.id))

	const subjectsBtn = document.createElement('a')
	subjectsBtn.className = 'tooltip-container'
	const subjectsIcon = document.createElement('span')
	subjectsIcon.className = 'material-icons'
	subjectsIcon.textContent = 'layers'
	const subjectsToolTip = document.createElement('div')
	subjectsToolTip.className = 'tooltip'
	subjectsToolTip.textContent = 'Materias'
	subjectsBtn.appendChild(subjectsToolTip)
	subjectsBtn.appendChild(subjectsIcon)

	subjectsBtn.href = `studentSubjects.html?studentId=${student.id}`

	td.appendChild(editBtn)
	td.appendChild(deleteBtn)
	td.appendChild(subjectsBtn)
	return td
}

function fillForm(student) {
	document.getElementById('studentId').value = student.id
	document.getElementById('fullname').value = student.fullname
	document.getElementById('email').value = student.email
	document.getElementById('age').value = student.age
}

async function confirmDelete(id) {
	if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return

	try {
		await studentsAPI.remove(id)
		loadStudents()
	} catch (err) {
		console.error('Error al borrar:', err)
		alert('Error al borrar el estudiante: ' + err)
	}
}
