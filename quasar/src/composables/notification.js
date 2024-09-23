import { Notify } from "quasar"

export const useNotification = () => {
	const notifySuccess = (message) => {
		return Notify.create({
			timeout: 1500,
			color: "green-4",
			textColor: "white",
			multiLine: true,
			icon: "cloud_done",
			position: "top-right",
			message
		})
	}

	const notifyError = (message) => {
		return Notify.create({
			timeout: 1500,
			color: "red-5",
			textColor: "white",
			multiline: true,
			icon: "warning",
			position: "top-right",
			message
		})
	}


	return { notifySuccess, notifyError }
}
