import { api } from "boot/axios"

export const getProducerIncomingRequests = async ({commit}, producerId) => {
	const response = await api.get(
		"personal/producers/relationRequests/incoming/" + producerId
	)
	commit("SET_PRODUCER_INCOMING_RELATION_REQUESTS", { ...response.data, producer_id: parseInt(producerId) })
}

export const acceptCoworkingRequest = async ({commit}, {producer_id, request_id}) => {
	const response = await api.post("personal/producers/relationRequests/acceptCoworkingRequest/" + request_id)
	commit("SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS", {
		producer_id,
		request_id: response.data.id,
		status: response.data.status
	})
}

export const rejectCoworkingRequest = async ({commit}, {producer_id, request_id}) => {
	const response = await api.post("personal/producers/relationRequests/rejectCoworkingRequest/" + request_id)
	commit("SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS", {
		producer_id,
		request_id: response.data.id,
		status: response.data.status
	})
}

export const getProducerUserList = async ({commit}, producer_id) => {
	const response = await api.get("personal/producers/" + producer_id + "/users")
	commit("SET_PRODUCER_USERS", {
		producer_id,
		users: response.data
	})
}

export const syncProducerUserPermissions = async ({commit}, { producer_id, user_id, permissions }) => {
	const response = await api.post(
		"personal/producers/permissions/" + producer_id + "/sync/" + user_id,
		permissions
	)
	commit("SYNC_PRODUCER_USER_PERMISSIONS", {
		producer_id,
		user_id,
		permissions:response.data
	})
}

export const getProducerProductList = async ({commit}, producer_id) => {
	const response = await api.get("personal/producers/" + producer_id + "/products")
	commit("SET_PRODUCER_PRODUCTS", {
		producer_id,
		products: response.data
	})
}
//todo - rollback on error
export const syncProducerProductCommonSettings = async({commit}, {producer_id, product_id, settings}) => {
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

/** uses 1 image at a time*/
export const syncProducerProductImagesSettings = async({commit}, {producer_id, product_id, image}) => {
	const response = await api.post(
		"/personal/producers/" + producer_id + "/products/" + product_id + "/syncImagesSettings",
		image,
	)
	commit("SYNC_PRODUCER_PRODUCT_IMAGES_SETTINGS", {
		producer_id,
		product_id,
		image: response.data
	})
}
