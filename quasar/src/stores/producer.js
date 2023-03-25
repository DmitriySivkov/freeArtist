import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"

export const useProducerStore = defineStore("producer", {
	state: () => ({
		data: {},
		detail: {},
		user_rights: [
			{
				id: 1,
				title: "owner",
				label: "владелец"
			},
			{
				id: 2,
				title: "coworker",
				label: "партнёр"
			}
		],
		is_fetching: false
	}),

	actions: {
		async getList() {
			const response = await api.get("producers")

			this.data = response.data
		},

		async getProducer(producerId) {
			const response = await api.get("producers/" + producerId)

			this.detail = response.data
		},

		async getProducerProductList (producer_id) {
			const response = await api.get("personal/producers/" + producer_id + "/products")

			const team_store = useTeamStore()

			team_store.user_teams.find((t) => t.detailed.id === producer_id).products = response.data
		},

		commitProducerProductFields({ producer_id, product_id, fields }) {
			const team_store = useTeamStore()

			let team = team_store.user_teams.find((t) => t.detailed.id === producer_id)

			let product = team.products.find((p) => p.id === product_id)

			Object.assign(product, fields)
		},

		async createProducerProduct({ producer_id, settings }) {
			const response = await api.post(
				"personal/producers/" + producer_id + "/products",
				{ settings }
			)

			const team_store = useTeamStore()

			team_store.user_teams.find((t) => t.detailed.id === producer_id)
				.products
				.unshift(response.data)

			return response
		},

		deleteProducerProduct({ product_id }) {
			return api.delete(
				"personal/products/" + product_id,
				{
					data: {
						product_id
					}
				})
		},

		commitRemoveProducerProduct({ producer_id, product_id }) {
			const team_store = useTeamStore()

			let producer = team_store.user_teams.find((t) => t.detailed.id === producer_id)

			const product_index = producer.products.findIndex((p) => p.id === product_id)

			producer.products.splice(product_index, 1)
		},

		updateProducerProduct({ product, changes }) {
			let data = new FormData()

			if (!!changes.composition) {
				product.composition = product.composition.map((ingredient) => {
					delete ingredient["is_new"]
					return ingredient
				})
			}

			data.append("product", JSON.stringify(product))
			data.append("changes", JSON.stringify(changes))

			if (product.committed_images) {
				for (let i in product.committed_images) {
					data.append("images[]", product.committed_images[i].instance)
				}
			}

			return api.post("personal/products/" + product.id, data)
		},

		async setProducerLogo({ producer_id, logo }) {
			const response = await api.post(
				"/personal/producers/" + producer_id + "/setLogo",
				logo,
			)

			const team_store = useTeamStore()

			team_store.user_teams.find((t) => t.detailed.id === producer_id)
				.detailed.logo = response.data
		},

		setFetchingState(is_fetching) {
			this.is_fetching = is_fetching
		}
	}
})
