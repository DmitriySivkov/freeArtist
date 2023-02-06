export const addToCart = ({commit}, payload) => {
	commit("ADD_TO_CART", payload)
}

export const setCart = ({commit}, payload) => {
	commit("SET_CART", payload)
}
