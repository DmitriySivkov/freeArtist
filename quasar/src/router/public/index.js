import Home from "src/pages/Home.vue"
import ProducerPublicList from "src/pages/producer/ProducerPublicList.vue"
import ProducerPublicProductList from "src/pages/producer/ProducerPublicProductList.vue"

export default [
	{
		name: "home",
		path: "/",
		component: Home,
		children: [
			{
				name: "producer_public_list",
				path: "",
				component: ProducerPublicList
			},
			{
				name: "producer_public_product_list",
				path: "products",
				component: ProducerPublicProductList
			}
		]
	}
]
