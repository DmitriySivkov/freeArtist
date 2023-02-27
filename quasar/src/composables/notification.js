import { Notify, useQuasar } from "quasar"

export const useNotification = () => {
	const $q = useQuasar()

	const notifySuccess = (message) => {
		return Notify.create({
			color: "green-4",
			textColor: "white",
			multiLine: true,
			icon: "cloud_done",
			position: $q.platform.is.desktop ? "top-right" : "bottom",
			message
		})
	}

	const notifyError = (message) => {
		return Notify.create({
			color: "red-5",
			textColor: "white",
			multiline: true,
			icon: "warning",
			position: $q.platform.is.desktop ? "top-right" : "bottom",
			message
		})
	}


	return { notifySuccess, notifyError }
}
