export const SET_ORDER_LIST = (state, payload) => {
	state.data = payload.map((item) => ({
		...item, products:JSON.parse(item.products)
	}))
}
