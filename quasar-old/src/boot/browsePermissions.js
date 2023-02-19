export default async ({ store }) => {
  	await store.dispatch("permission/getList")
}
