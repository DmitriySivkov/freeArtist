import { useRoleStore } from "src/stores/role";

export default async ({ store }) => {
  const role_store = useRoleStore(store)

  await role_store.getList()
}
