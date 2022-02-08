<template>
	<q-item
		clickable
		@click="this.changeRoute"
	>
		<q-item-section
			v-if="this.icon"
			avatar
		>
			<q-icon
				:name="this.icon"
				size="md"
			/>
		</q-item-section>
		<q-item-section>
			<q-item-label class="text-subtitle1">
				{{ title }}
			</q-item-label>
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
			default: ""
		},
		icon: {
			type: String,
			default: ""
		},
		dispatch: {
			type: String,
			default: ""
		},
		notification: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const $router = useRouter()
		const $store = useStore()
		return {
			changeRoute() {
				props.dispatch === "" ?
					$router.push(props.link) :
					$store.dispatch(props.dispatch).then(() => {
						$router.push(props.link)
						if (Object.keys(props.notification).length > 0)
							Notify.create(props.notification)
					})
			},
		}
	},
}
</script>
