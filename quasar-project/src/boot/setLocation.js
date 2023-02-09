import { useUserStore } from "src/stores/user"

export default async ({ store }) => {
  const user_store = useUserStore(store)
	await user_store.setLocation()
}
