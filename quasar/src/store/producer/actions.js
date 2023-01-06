import { api } from "boot/axios"

export const getList = async ({commit}) => {
	const response = await api.get("producers")
	commit("SET_PRODUCERS", response.data)
}

export const getProducer = async ({commit}, producerId) => {
	const response = await api.get("producers/" + producerId)
	commit("SET_CURRENT_PRODUCER", response.data)
}

export const getProducerProductList = async ({commit}, producer_id) => {
	const response = await api.get("personal/producers/" + producer_id + "/products")
	commit("team/SET_PRODUCER_PRODUCTS", {
		producer_id,
		products: response.data
	}, { root:true })
}
//todo - rollback on error
export const syncProducerProductCommonSettings = async({commit}, { producer_id, product_id, settings }) => {
	await api.post(
		"personal/producers/" + producer_id + "/products/" + product_id + "/syncCommonSettings",
		{
			settings: settings
		}
	)
	commit("SYNC_PRODUCER_PRODUCT_COMMON_SETTINGS", {
		producer_id,
		product_id,
		settings
	})
}

export const createProducerProduct = async({commit}, { producer_id, settings }) => {
	const response = await api.post(
		"personal/producers/" + producer_id + "/products",
		{
			settings
		}
	)
	commit("CREATE_PRODUCER_PRODUCT", {
		producer_id,
		product: response.data
	})
	return response
}

export const deleteProducerProduct = async({commit}, { producer_id, product_id }) => {
	await api.delete(
		"personal/producers/" + producer_id + "/products/" + product_id,
		{
			data: {
				producer_id,
				product_id
			}
		}
	)
	commit("DELETE_PRODUCER_PRODUCT", {
		producer_id,
		product_id
	})
}

export const syncProducerProductCompositionSettings = async({commit}, {producer_id, product_id, composition}) => {
	const response = await api.post(
		"personal/producers/" + producer_id + "/products/" + product_id + "/syncCompositionSettings",
		{ composition }
	)
	commit("SYNC_PRODUCER_PRODUCT_COMPOSITION_SETTINGS", {
		producer_id,
		product_id,
		composition: response.data
	})
}

export const addProducerProductImage = async({commit}, { producer_id, product_id, image }) => {
	const response = await api.post(
		"/personal/producers/" + producer_id + "/products/" + product_id + "/addImage",
		image,
	)
	commit("ADD_PRODUCER_PRODUCT_IMAGE", {
		producer_id,
		product_id,
		image: response.data
	})
}

export const setProducerLogo = async({commit}, { producer_id, logo }) => {
	const response = await api.post(
		"/personal/producers/" + producer_id + "/setLogo",
		logo,
	)
	commit("SET_PRODUCER_LOGO", {
		producer_id,
		logo: response.data
	})
}
