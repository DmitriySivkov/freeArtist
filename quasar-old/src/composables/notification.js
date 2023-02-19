import { Notify } from "quasar"

export const useNotification = () => {

	const notifySuccess = (message) => {
		return Notify.create({
			color: "green-4",
			textColor: "white",
			multiLine: true,
			icon: "cloud_done",
			message
		})
	}

	const notifyError = (message) => {
		return Notify.create({
			color: "red-5",
			textColor: "white",
			multiline: true,
			icon: "warning",
			message
		})
	}


	return { notifySuccess, notifyError }
}
