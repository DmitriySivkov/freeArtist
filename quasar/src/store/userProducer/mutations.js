export const SET_USER_PRODUCER = (state, producer) => {
	if (state.producers.length > 0)
		state.producers = [...state.producers, producer]
	else
		state.producers = producer
}

export const SET_PRODUCER_PRODUCTS = (state, {products, producer_id}) => {
	state.producers.find((producer) => producer.id === producer_id).products = products
}

export const SET_PRODUCER_INCOMING_RELATION_REQUESTS = (state, payload) => {
	let producer = state.producers.find((team) => team.id === payload.producer_id)
	if (producer.hasOwnProperty("incoming_coworking_requests")) {
		producer.incoming_coworking_requests = [...payload.incoming_coworking_requests, producer.incoming_coworking_requests]
	} else {
		producer.incoming_coworking_requests = payload.incoming_coworking_requests
	}
}

export const EMPTY_USER_PRODUCER = (state) => {
	state.producers = []
}

export const SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS = (state, {producer_id, request_id, status}) => {
	const requests = state.producers.find((producer) => producer.id === producer_id)
		.requests

	requests.data
		.incoming_coworking_requests
		.find((request) => request.id === request_id)
		.status = status

	requests.total_pending_request_count--
}

export const SET_PRODUCER_USERS = (state, {producer_id, users}) => {
	state.producers.find((producer) => producer.id === producer_id).users = users
}

export const SYNC_PRODUCER_USER_PERMISSIONS = (state, {producer_id, user_id, permissions}) => {
	state.producers.find((producer) => producer.id === producer_id)
		.users
		.find((user) => user.id === user_id)
		.permissions = permissions
}

export const SYNC_PRODUCER_PRODUCT_COMMON_SETTINGS = (state, {producer_id, product_id, settings}) => {
	const product = state.producers.find((producer) => producer.id === producer_id)
		.products
		.find((product) => product.id === product_id)

	Object.assign(product, settings)
}

export const SYNC_PRODUCER_PRODUCT_COMPOSITION_SETTINGS = (state, {producer_id, product_id, composition}) => {
	state.producers.find((producer) => producer.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.composition = composition
}

/** uses 1 image at a time*/
export const SYNC_PRODUCER_PRODUCT_IMAGES_SETTINGS = (state, {producer_id, product_id, image}) => {
	state.producers.find((producer) => producer.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.images
		.unshift(image)
}
