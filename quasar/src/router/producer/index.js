import ProducerPublicProductList from "src/pages/producer/ProducerPublicProductList.vue"
import ProducerPublicProductsDetail from "src/pages/producer/ProducerPublicProductsDetail.vue"

export default [
	{
		name: "producer_public_products",
		path: "/producers/:producer_id/products",
		component: ProducerPublicProductList,
	},
	{
		name: "producer_public_products_detail",
		path: "/producers/:producer_id/products/:product_id",
		component: ProducerPublicProductsDetail,
	}
]
