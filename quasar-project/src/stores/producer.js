import { defineStore } from "pinia"
import { api } from "src/boot/axios";
import { useTeamStore } from "src/stores/team";

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
    ]
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
      team_store.setProducerProducts({ producer_id, products: response.data })
    },

    //todo - rollback on error
    async syncProducerProductCommonSettings({commit}, { producer_id, product_id, settings }) {
      await api.post(
        "personal/producers/" + producer_id + "/products/" + product_id + "/syncCommonSettings",
        {
          settings: settings
        }
      )

      const team_store = useTeamStore()
      team_store.syncProducerProductCommonSettings({ producer_id, product_id, settings})
    },

    async createProducerProduct({commit}, { producer_id, settings }) {
      const response = await api.post(
        "personal/producers/" + producer_id + "/products",
        { settings }
      )

      const team_store = useTeamStore()

      team_store.createProducerProduct({
        producer_id,
        product: response.data
      })

      return response
    },

    async deleteProducerProduct({commit}, { producer_id, product_id }) {
      await api.delete(
        "personal/producers/" + producer_id + "/products/" + product_id,
        {
          data: {
            producer_id,
            product_id
          }
        })

      const team_store = useTeamStore()

      team_store.deleteProducerProduct({
        producer_id,
        product_id
      })
    },

    async syncProducerProductCompositionSettings({producer_id, product_id, composition}) {
      const response = await api.post(
        "personal/producers/" + producer_id + "/products/" + product_id + "/syncCompositionSettings",
        { composition }
      )

      const team_store = useTeamStore()

      team_store.syncProductProductCompositionSettings({
        producer_id,
        product_id,
        composition: response.data
      })
    },

    async addProducerProductImage({commit}, { producer_id, product_id, image }) {
      const response = await api.post(
        "/personal/producers/" + producer_id + "/products/" + product_id + "/addImage",
        image,
      )

      const team_store = useTeamStore()

      team_store.addProducerProductImage({
        producer_id,
        product_id,
        image: response.data
      })
    },

    async setProducerLogo({commit}, { producer_id, logo }) {
      const response = await api.post(
        "/personal/producers/" + producer_id + "/setLogo",
        logo,
      )

      const team_store = useTeamStore()

      team_store.setProducerLogo({
        producer_id,
        logo: response.data
      })
    }
	}
})
