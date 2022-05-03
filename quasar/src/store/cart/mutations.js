export const ADD_TO_CART = (state, payload) => {

	Object.keys(payload.producerProductList).forEach((productId) => {
		if (payload.producerProductList[productId] === 0)
			delete payload.producerProductList[productId]
	})

	let producerProductListFiltered = {[payload.producerId]: payload.producerProductList}

	state.data = {...state.data, ...producerProductListFiltered}
}
