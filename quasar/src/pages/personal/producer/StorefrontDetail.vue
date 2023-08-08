<template>
	<!-- todo - return carousel & drag block from history -->
	<div class="column absolute fit justify-center">
		<div class="row col-6 justify-center">
			<div class="col-xs col-lg-8">
				<q-card
					class="column fit"
					:class="[
						`home__card_${isWidthThreshold ? 'expand' : 'shrink'}`,
						{'bg-green-6 text-white': is_dragging}
					]"
				>

					<cropper
						v-if="tmp_image"
						ref="cropper"
						class="cropper full-width"
						backgroundClass="bg-green-4"
						:src="tmp_image"
						:stencil-props="{aspectRatio: 16/9}"
						style="z-index:9999"
					/>

					<q-img
						v-else-if="team.detailed.storefront_image"
						no-spinner
						:src="backend_server + '/storage/' + team.detailed.storefront_image.path"
						fit="contain"
						:ratio="16/9"
					/>

					<div
						v-else
						class="text-center col-grow flex flex-center"
					>
						Выберите изображение <br /> или переместите в эту область
					</div>

					<div class="col-shrink row">
						<span class="text-h6 q-pa-md">{{ team.display_name }}</span>
					</div>

				</q-card>

			</div>
		</div>
	</div>

	<div
		v-if="tmp_image"
		class="row q-col-gutter-md q-px-md"
	>
		<div class="col-6">
			<q-btn
				color="primary"
				icon="done"
				class="q-pa-lg full-height full-width"
				@click="loadImage"
			/>
		</div>
		<div class="col-6">
			<q-btn
				color="red"
				icon="clear"
				class="q-pa-lg full-height full-width"
				@click="cancelImage"
			/>
		</div>
	</div>

	<q-file
		v-model="image"
		ref="file_picker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="addImage"
	/>
</template>

<script setup>
import { useQuasar } from "quasar"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"
import { useProducerStore } from "src/stores/producer"
import { usePermissionStore } from "src/stores/permission"
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { Cropper } from "vue-advanced-cropper"

const $q = useQuasar()
const $router = useRouter()
const user_store = useUserStore()
const team_store = useTeamStore()
const producer_store = useProducerStore()
const permission_store = usePermissionStore()

const backendServer = process.env.BACKEND_SERVER

const team = computed(() =>
	team_store.user_teams.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
)

const image = ref(null)

const cropper = ref(null)

const file_picker = ref(null)
const is_dragging = ref(false)
const is_loading = ref(false)

const is_team_admin = computed(() => user_store.data.id === team.value.user_id)

const can_manage_storefront = computed(() =>
	permission_store.user_permissions.filter((p) => p.team_id === parseInt(team.value.id))
		.map((p) => p.name)
		.includes("producer_storefront")
)

const backend_server = process.env.BACKEND_SERVER
const { notifySuccess, notifyError } = useNotification()

const drop = (e) => {
	is_dragging.value = false
	file_picker.value.addFiles([e.dataTransfer.files[0]])
}

const tmp_image = ref(null)

const addImage = () => {
	tmp_image.value = URL.createObjectURL(image.value)
}

const cancelImage = () => {
	tmp_image.value = null
	image.value = null
}

const loadImage = async() => {
	is_loading.value = true

	const { canvas } = cropper.value.getResult()

	let form_data = new FormData()

	const blob = await new Promise(resolve => canvas.toBlob(resolve))

	form_data.append("storefront_image", blob)

	// todo validate with q-file help
	const promise = producer_store.setProducerStorefrontImage({
		storefront_image: form_data,
		producer_id: team.value.detailed_id
	})

	promise.then((response) => {
		team_store.setTeamFields({
			team_id: team.value.id,
			fields: {
				storefront_image: response.data
			},
			detailed_id: team.value.detailed_id
		})

		notifySuccess("Изображение успешно загружено")
	})

	promise.catch((error) => {
		notifyError(error.response.data)
	})

	promise.finally(() => {
		is_loading.value = false
		is_dragging.value = false
		tmp_image.value = null
	})
}

const showFilePrompt = () => {
	file_picker.value.pickFiles()
}

const isWidthThreshold = computed(() => $q.screen.width >= $q.screen.sizes.sm)

</script>
