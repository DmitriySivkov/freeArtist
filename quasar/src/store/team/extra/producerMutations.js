// todo - whenever new mutation is created here - dont forget to add it to team mutations
export const SET_PRODUCER_PRODUCTS = (state, { products, producer_id }) => {
	state.user_teams.find((t) => t.detailed.id === producer_id).products = products
}

export const SET_PRODUCER_INCOMING_RELATION_REQUESTS = (state, payload) => {
	let producer = state.user_teams.find((t) => t.detailed.id === payload.producer_id)

	producer.requests.total_pending_request_count++
	/** later there will be more types of incoming requests **/
	producer.requests.data.incoming_coworking_requests = [
		...producer.requests.data.incoming_coworking_requests,
		...payload.incoming_coworking_requests
	]
}

export const SYNC_PRODUCER_PRODUCT_COMMON_SETTINGS = (state, { producer_id, product_id, settings }) => {
	const product = state.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)

	Object.assign(product, settings)
}

export const SYNC_PRODUCER_PRODUCT_COMPOSITION_SETTINGS = (state, { producer_id, product_id, composition }) => {
	state.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.composition = composition
}

export const CREATE_PRODUCER_PRODUCT = (state, { producer_id, product }) => {
	state.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.unshift(product)
}

export const DELETE_PRODUCER_PRODUCT = (state, { producer_id, product_id }) => {
	const producer = state.user_teams.find((t) => t.detailed.id === producer_id)
	producer.products = producer.products.filter((product) => product.id !== product_id)
}

export const ADD_PRODUCER_PRODUCT_IMAGE = (state, { producer_id, product_id, image }) => {
	state.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.images
		.unshift(image)
}

export const SET_PRODUCER_LOGO = (state, { producer_id, logo }) => {
	state.user_teams.find((t) => t.detailed.id === producer_id)
		.detailed.logo = logo
}
