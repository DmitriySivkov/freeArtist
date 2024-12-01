import { LocalStorage } from "quasar"
import { SecureStoragePlugin } from "capacitor-secure-storage-plugin"
import { Platform } from "quasar"
import { useNotification } from "src/composables/notification"

/**
 * quasar storage can work with any types
 * capacitor store only store strings
 **/
export const useStorage = () => {
	const { notifyError } = useNotification()

	const set = async({ key, value }) => {
		try {
			if (Platform.is.capacitor) {
				await SecureStoragePlugin.set({
					key,
					value: JSON.stringify(value)
				})
			} else {
				LocalStorage.set(key, value)
			}
		} catch (error) {
			notifyError("Ошибка хранилища: код 1")
		}
	}

	const get = async(key) => {
		try {
			if (Platform.is.capacitor) {
				let data = await SecureStoragePlugin.get({ key })
				return JSON.parse(data.value)
			} else {
				return LocalStorage.getItem(key)
			}
		} catch (error) {
			notifyError("Ошибка хранилища: код 2")
		}
	}

	const has = async(key) => {
		if (Platform.is.capacitor) {
			try {
				// secure-storage-plugin does not have 'has' method
				await SecureStoragePlugin.get({ key })
				return true
			} catch (error) {
				return false
			}
		} else {
			return LocalStorage.has(key)
		}
	}

	return {
		set,
		get,
		has
	}
}
