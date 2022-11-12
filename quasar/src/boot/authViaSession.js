import { Loading } from "quasar"

export default async ({ store }) => {
	Loading.show({
		spinnerColor: "primary",
	})
	await store.dispatch("user/authViaSession")
}
