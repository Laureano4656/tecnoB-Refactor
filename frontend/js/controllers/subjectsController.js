import { subjectsAPI } from '../api/subjectsAPI.js'

//frontDispatcher_2.0

function getFormData() {
	return {
		subject_id: document.getElementById('subjectId').value.trim(),
		name: document.getElementById('name').value.trim(),
		description: document.getElementById('description').value.trim(),
		year: parseInt(document.getElementById('year').value.trim()),
		professor_name: document.getElementById('professor_name').value.trim(),
	}
}

function clearForm() {
	document.getElementById('subjectForm').reset()
	document.getElementById('subjectId').value = ''
}

function setupFormHandler() {
	const subjectForm = document.getElementById('subjectForm')
	subjectForm.addEventListener('submit', async e => {
		e.preventDefault()
		const subject = getFormData()

		try {
			if (subject.subject_id) {
				await subjectsAPI.update(subject)
			} else {
				await subjectsAPI.create(subject)
			}
			clearForm()
			loadSubjects()
		} catch (err) {
			const errorDiv = document.getElementById('errorMessage')
			errorDiv.textContent = 'Error en la operacion: ' + err
			errorDiv.style.display = 'block'
			setTimeout(() => {
				errorDiv.style.display = 'none'
			}, 3000)
			console.error(err)
		}
	})
}

async function loadSubjects() {
	try {
		const subjects = await subjectsAPI.fetchAll()
		renderSubjectTable(subjects)
	} catch (err) {
		console.error(err.message)
	}
}

function createCell(text) {
	const td = document.createElement('td')
	td.textContent = text
	return td
}

function fillForm(subject) {
	document.getElementById('name').value = subject.name
	document.getElementById('description').value = subject.description
	document.getElementById('year').value = subject.year
	document.getElementById('professor_name').value = subject.professor_name
	document.getElementById('subjectId').value = subject.subject_id
}

function createActionsCell(subject) {
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
	editBtn.addEventListener('click', () => fillForm(subject))

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
	deleteBtn.addEventListener('click', () => deleteSubject(subject.subject_id))

	tdActions.appendChild(editBtn)
	tdActions.appendChild(deleteBtn)

	return tdActions
}

function renderSubjectTable(subjects) {
	const subjectTableBody = document.getElementById('subjectTableBody')
	subjectTableBody.replaceChildren()
	//acá innerHTML es seguro a XSS porque no hay entrada de usuario
	//igual no lo uso.
	//subjectTableBody.innerHTML = "";

	subjects.forEach(subject => {
		const tr = document.createElement('tr')

		tr.appendChild(createCell(subject.name))
		tr.appendChild(createCell(subject.description))
		tr.appendChild(createCell(subject.year))
		tr.appendChild(createCell(subject.professor_name))
		tr.appendChild(createActionsCell(subject))
		subjectTableBody.appendChild(tr)
	})
}
async function deleteSubject(id) {
	if (!confirm('¿Seguro que querés borrar esta materia?')) return

	try {
		const response = await subjectsAPI.remove(id)
		loadSubjects()
	} catch (err) {
		const errorDiv = document.getElementById('errorMessage')
		errorDiv.textContent = 'Error en la operacion: ' + err
		errorDiv.style.display = 'block'
		setTimeout(() => {
			errorDiv.style.display = 'none'
		}, 3000)
		console.error(err)
	}
}
document.addEventListener('DOMContentLoaded', () => {
	setupFormHandler()
	loadSubjects()
})
