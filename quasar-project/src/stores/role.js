import { defineStore } from "pinia"
import { api } from "src/boot/axios";

export const useRoleStore = defineStore("role", {
	state: () => ({
    data: {},
    types: {
      producer: 1,
      advertiser: 2,
      guarantor: 3,
    }
	}),

	actions: {
    async getList() {
      const response = await api.get("roles")

      this.data = response.data
    }
	}
})
