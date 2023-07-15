import { useUserStore } from "src/stores/user"
import { LocalStorage } from "quasar"
import { LOCATION_RANGE, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

export default async ({ store }) => {
	const userStore = useUserStore(store)

	if (LocalStorage.has("location")) {
		let location = LocalStorage.getItem("location")

		userStore.setLocation(location)

		userStore.setLocationRange(
			location.id === LOCATION_UNKNOWN_ID ?
				LOCATION_RANGE.all :
				LOCATION_RANGE.nearby
		)
	}
}
