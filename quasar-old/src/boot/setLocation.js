export default async ({ store }) => {
	await store.dispatch("user/setLocation")
}
