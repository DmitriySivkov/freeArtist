<template>
	<q-item
		clickable
		@click="this.changeRoute"
	>
		<q-item-section
			v-if="props.link.meta.icon"
			avatar
		>
			<q-icon
				:name="props.link.meta.icon"
				size="md"
			/>
		</q-item-section>
		<q-item-section>
			<q-item-label class="text-subtitle1">
				{{ props.link.meta.route_name }}
			</q-item-label>
			<q-item-label caption>
				{{ props.link.meta.caption }}
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
		link: {
			type: Object,
			default: () => ({})
		},
	},
	setup(props) {
		const $router = useRouter()
		const $store = useStore()

		const changeRoute = () => {
			if (props.link.name === "logout") {
				$router.push({name: props.link.meta.redirect})
				$store.dispatch(props.link.meta.dispatch).then(() => {
					if (Object.keys(props.link.meta.notification))
						Notify.create(props.link.meta.notification)
				})
			}
			else {
				!props.link.meta.dispatch ?
					$router.push({name: props.link.name}) :
					$store.dispatch(props.link.meta.dispatch).then(() => {
						$router.push({name: props.link.meta.redirect})
						if (Object.keys(props.link.meta.notification))
							Notify.create(props.link.meta.notification)
					})
			}
		}
		return {
			props,
			changeRoute
		}
	}
}
</script>
