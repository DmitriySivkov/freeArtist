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

		commitProducerProductFields({ producer_id, product_id, fields, tmp_uuid }) {
			const team_store = useTeamStore()

			let team = team_store.user_teams.find((t) => t.detailed.id === producer_id)

			let product = team.products.find((p) => p.id === product_id)

			if (tmp_uuid) {
				delete product.tmp_uuid
			}

			Object.assign(product, fields)
		},

		commitProducerNewProduct({ producer_id, product, tmp_uuid }) {
			const team_store = useTeamStore()

			let team = team_store.user_teams.find((t) => t.detailed.id === producer_id)

			if (tmp_uuid) {
				let index = team.products.findIndex((p) => p.tmp_uuid === tmp_uuid)

				if (index !== -1) {
					team.products[index] = product
				}

				return
			}

			team.products.unshift(product)
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

		commitRemoveProducerProduct({ producer_id, product_id, tmp_uuid }) {
			const team_store = useTeamStore()

			let producer = team_store.user_teams.find((t) => t.detailed.id === producer_id)

			let product_index

			if (product_id) {
				product_index = producer.products.findIndex((p) => p.id === product_id)
			} else {
				product_index = producer.products.findIndex((p) => p.tmp_uuid === tmp_uuid)
			}

			producer.products.splice(product_index, 1)
		},

		createProducerProduct({ team_id, product }) {
			let data = new FormData()

			if (product.composition.length > 0) {
				product.composition = product.composition.map((ingredient) => {
					delete ingredient["is_new"]
					return ingredient
				})
			}

			data.append("product", JSON.stringify(product))
			data.append("team_id", team_id)

			if (product.committed_images) {
				for (let i in product.committed_images) {
					data.append("images[]", product.committed_images[i].instance)
				}
			}

			return api.post("personal/products", data)
		},

		updateProducerProduct({ product }) {
			let data = new FormData()

			data.append("product", JSON.stringify(product))

			for (let i in product.committed_images) {
				data.append("images[]", product.committed_images[i].instance)
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
