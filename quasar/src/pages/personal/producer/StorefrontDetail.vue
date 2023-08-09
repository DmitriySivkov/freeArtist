<template>
	<!-- todo - 1) - fetch some product images 2) - bring home page cards to this view -->
	<div class="column absolute fit">
		<div class="col row flex-center">
			<div class="col-xs-12 col-md-9 col-xl-8">
				<q-card
					:class="`home__card_${isWidthThreshold ? 'expand' : 'shrink'}`"
				>
					<div class="row">
						<div class="col-xs-12 col-sm-8">
							<q-responsive
								:ratio="16/9"
								:class="{'bg-green-6 text-white': is_dragging}"
							>
								<div>
									<Cropper
										v-if="tmp_image"
										ref="cropper"
										class="absolute"
										:src="tmp_image"
										:stencil-props="{aspectRatio: 16/9}"
									/>

									<q-img
										v-else-if="team.detailed.storefront_image"
										no-spinner
										class="absolute"
										:src="backend_server + '/storage/' + team.detailed.storefront_image.path"
										fit="contain"
										style="border-radius: 4px 0 0 0"
									/>

									<div
										v-else
										class="absolute fit flex flex-center text-center"
									>
										Выберите изображение <br /> или переместите в эту область
									</div>

									<div
										v-if="can_manage_storefront || is_team_admin"
										class="absolute fit cursor-pointer"
										@dragenter.prevent="is_dragging = true"
										@dragleave.prevent="is_dragging = false"
										@dragover.prevent
										@drop.prevent="drop"
										@click="showFilePrompt"
									></div>
									<q-inner-loading
										:showing="is_loading"
										style="z-index:99999"
									>
										<q-spinner-gears
											size="50px"
											color="primary"
										/>
									</q-inner-loading>
								</div>
							</q-responsive>
						</div>
						<q-carousel
							v-model="slide"
							:vertical="isWidthThreshold"
							class="col-xs-12 col-sm-4 bg-secondary home__card-carousel"
							transition-prev="fade"
							transition-next="fade"
							swipeable
							animated
							control-color="dark"
							infinite
							:arrows="true"
						>
							<q-carousel-slide
								name="products"
								class="q-pa-none"
							>
								<div
									v-if="!isWidthThreshold"
									class="col row justify-center"
								>
									<div class="col-xs-9">
										<div class="row justify-center q-gutter-x-xs">
											<q-img
												v-for="n in Math.ceil(3/2)"
												:key="n"
												no-spinner
												class="col"
												fit="cover"
												:src="'/no-image.png'"
												:ratio="4/3"
											/>
										</div>
									</div>
								</div>
								<div
									v-else
									class="column fit justify-center q-gutter-xs q-mx-none"
								>
									<q-img
										v-for="n in Math.ceil(3/2)"
										:key="n"
										no-spinner
										class="col-4 q-mx-none"
										fit="contain"
										:src="'/no-image.png'"
									/>
								</div>
							</q-carousel-slide>
						</q-carousel>
						<div class="row">
							<span class="text-h6 q-pa-md">{{ team.display_name }}</span>
						</div>
					</div>
				</q-card>

				<div
					v-if="tmp_image"
					class="row q-gutter-xs q-mt-xs"
				>
					<q-btn
						color="primary"
						icon="done"
						class="col q-pa-lg"
						@click="loadImage"
					/>
					<q-btn
						color="red"
						icon="clear"
						class="col q-pa-lg"
						@click="cancelImage"
					/>
				</div>

			</div>
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

const slide = ref("products")

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
