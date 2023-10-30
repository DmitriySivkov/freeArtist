import { Notify, useQuasar } from "quasar"

export const useNotification = () => {
	const $q = useQuasar()

	const notifySuccess = (message) => {
		return Notify.create({
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
