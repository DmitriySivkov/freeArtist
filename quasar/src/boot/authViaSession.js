export default async ({ store }) => {
	await store.dispatch("user/authViaSession")
}
