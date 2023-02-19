import { VMoney } from "v-money"
export default ({ app }) => {
	app.directive("money", VMoney)
}
