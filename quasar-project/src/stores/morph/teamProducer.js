export const setProducerProducts = ({products, producer_id}) => {
	this.user_teams.find((t) => t.detailed.id === producer_id).products = products
}

export const syncProducerProductCommonSettings = ({producer_id, product_id, settings}) => {
	const product = this.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)

	Object.assign(product, settings)
}

export const syncProducerProductCompositionSettings = ({producer_id, product_id, composition}) => {
	this.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.composition = composition
}

export const createProducerProduct = ({producer_id, product}) => {
	this.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.unshift(product)
}

export const deleteProducerProduct = ({producer_id, product_id}) => {
	const producer = this.user_teams.find((t) => t.detailed.id === producer_id)

	producer.products = producer.products.filter((product) => product.id !== product_id)
}

export const addProducerProductImage = ({producer_id, product_id, image}) => {
	this.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.images
		.unshift(image)
}

export const setProducerLogo = ({producer_id, logo}) => {
	this.user_teams.find((t) => t.detailed.id === producer_id)
		.detailed.logo = logo
}
