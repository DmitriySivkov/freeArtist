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

		async setProducerLogo({ producer_id, logo }) {
			return api.post(
				"/personal/producers/" + producer_id + "/setLogo",
				logo,
			)
		},

		async setProducerStorefrontImage({ producer_id, storefront_image }) {
			return api.post(
				"/personal/producers/" + producer_id + "/setStorefrontImage",
				storefront_image,
			)
		},

		setFetchingState(is_fetching) {
			this.is_fetching = is_fetching
		}
	}
})
