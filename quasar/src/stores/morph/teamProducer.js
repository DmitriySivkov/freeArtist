import { useTeamStore } from "src/stores/team"

export const setProducerProducts = ({products, producer_id}) => {
	const team_store = useTeamStore()

	team_store.user_teams.find((t) => t.detailed.id === producer_id).products = products
}

export const syncProducerProductCommonSettings = ({producer_id, product_id, settings}) => {
	const team_store = useTeamStore()

	const product = team_store.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)

	Object.assign(product, settings)
}

export const syncProducerProductCompositionSettings = ({producer_id, product_id, composition}) => {
	const team_store = useTeamStore()

	team_store.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.composition = composition
}

export const createProducerProduct = ({producer_id, product}) => {
	const team_store = useTeamStore()

	team_store.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.unshift(product)
}

export const deleteProducerProduct = ({producer_id, product_id}) => {
	const team_store = useTeamStore()

	const producer = team_store.user_teams.find((t) => t.detailed.id === producer_id)

	producer.products = producer.products.filter((product) => product.id !== product_id)
}

export const addProducerProductImage = ({producer_id, product_id, image}) => {
	const team_store = useTeamStore()

	team_store.user_teams.find((t) => t.detailed.id === producer_id)
		.products
		.find((product) => product.id === product_id)
		.images
		.unshift(image)
}

export const setProducerLogo = ({producer_id, logo}) => {
	const team_store = useTeamStore()

	team_store.user_teams.find((t) => t.detailed.id === producer_id)
		.detailed.logo = logo
}
