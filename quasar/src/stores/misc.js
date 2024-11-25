import { defineStore } from "pinia"

export const useMiscStore = defineStore("misc", {
	state: () => ({
		homePageProducers: [],
		homePageSelectedProducer: null,
		homePageVerticalScroll: 0,
		homePageSelectedCategories: [],
	}),

	actions: {
		setHomePageProducers(producers) {
			this.homePageProducers = [...this.homePageProducers, ...producers]
		},

		emptyHomePageProducers() {
			this.homePageProducers = []
		},

		setHomePageSelectedProducer(producerId) {
			this.homePageSelectedProducer = producerId
		},

		setHomePageVerticalScroll(scrollPosition) {
			this.homePageVerticalScroll = scrollPosition
		},

		setHomePageSelectedCategories(categories) {
			this.homePageSelectedCategories = categories
		},

		emptyHomePageSelectedCategories() {
			this.homePageSelectedCategories = []
		},
	}
})
