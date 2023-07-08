import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const useProducerStore = defineStore("producer", {
	state: () => ({
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
		async getProducer(producerId) {
			const response = await api.get("producers/" + producerId)

			this.detail = response.data
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
