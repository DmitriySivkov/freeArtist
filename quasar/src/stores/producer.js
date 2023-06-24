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
