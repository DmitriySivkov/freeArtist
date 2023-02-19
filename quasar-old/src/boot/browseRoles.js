export default async ({ store }) => {
  	await store.dispatch("role/getList")
}
