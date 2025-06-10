import { createAPI } from './apiFactory.js'
const baseAPI = createAPI('studentsSubjects')

export const studentsSubjectsAPI = {
	...baseAPI, // hereda fetchAll, create, update, remove

	// método adicional personalizado
	async fetchByStudentId(id) {
		const res = await fetch(`../../backend/server.php/studentsSubjects?id=${id}`)
		if (!res.ok)
			throw new Error('No se pudieron obtener asignaciones del estudiante')
		return await res.json()
	},
}
