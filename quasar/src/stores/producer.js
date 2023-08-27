import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const useProducerStore = defineStore("producer", {
	state: () => ({
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
	}),

	actions: {
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
	}
})
