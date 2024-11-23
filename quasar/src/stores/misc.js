import { defineStore } from "pinia"

export const useMiscStore = defineStore("misc", {
	state: () => ({
		homePageProducers: [],
		homePageSelectedProducer: {
			id: null,
			scrollPosition: null
		}
	}),

	actions: {
		setHomePageProducers(producers) {
			this.homePageProducers = [...this.homePageProducers, ...producers]
		},

		emptyHomePageProducers() {
			this.homePageProducers = []
		},

		setHomePageSelectedProducer({ id, scrollPosition }) {
			this.homePageSelectedProducer = { id, scrollPosition }
		},

		emptyHomePageSelectedProducer() {
			this.homePageProducers = {
				id: null,
				scrollPosition: null
			}
		}
	}
})
