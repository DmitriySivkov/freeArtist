<template>
	<q-item
		v-if="!this.apiCall"
		:to="this.link"
	>
		<q-item-section
			v-if="this.icon"
			avatar
		>
			<q-icon :name="this.icon" />
		</q-item-section>
		<q-item-section>
			<q-item-label>{{ title }}</q-item-label>
			<q-item-label caption>
				{{ caption }}
			</q-item-label>
		</q-item-section>
	</q-item>

	<q-item
		v-else
		clickable
		@click="makeApiCall"
	>
		<q-item-section
			v-if="this.icon"
			avatar
		>
			<q-icon :name="this.icon" />
		</q-item-section>
		<q-item-section>
			<q-item-label>{{ title }}</q-item-label>
			<q-item-label caption>
				{{ caption }}
			</q-item-label>
		</q-item-section>
	</q-item>
</template>

<script>
import { useStore } from "vuex"
import { useRouter } from "vue-router"
import { Notify } from "quasar"

export default {
	name: "EssentialLink",
	props: {
		title: {
			type: String,
			required: true
		},
		caption: {
			type: String,
			default: ""
		},
		link: {
			type: String,
			default: "#"
		},
		icon: {
			type: String,
			default: ""
		},
		apiCall: {
			type: Function
		}
	},
	setup(props) {
		const $router = useRouter()
		const $store = useStore()
		return {
			async makeApiCall() {
				await props.apiCall($store).then(() => {
					Notify.create({
						color: "green-4",
						textColor: "white",
						icon: "cloud_done",
						message: "Успешный логаут"
					})
					$router.replace("auth")
				})
			}
		}
	},
}
</script>
