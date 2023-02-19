import { useProducerStore } from "src/stores/producer"

export default async ({ store }) => {
	const producer_store = useProducerStore(store)

	await producer_store.getList()
}
