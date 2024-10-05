<script setup>
import { useDialogPluginComponent } from "quasar"
import { computed, ref } from "vue"
import { api } from "src/boot/axios"
import { useQuasar } from "quasar"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const $q = useQuasar()
const isMobileWidthThreshold = computed(() => $q.screen.width < $q.screen.sizes.md)

const team = ref(null)
const message = ref("")

const teamList = ref([])

const isLoading = ref(false)

const loadTeamList = async (query, doneFn, abortFn) => {
	// todo - на мобилах подгружает результаты только после пробела либо нажатия кнопки "поиск"
	if (query.length < 1) {
		abortFn()
		return
	}

	const response = await api.get("personal/teams",{
		params: {
			q: query,
			exclude_personal: true
		}
	})

	doneFn(() => {
		teamList.value = response.data.map((team) => {
			return {
				label: team.display_name,
				id: team.id
			}
		})
	})
}

const submitRelationRequest = () => {
	isLoading.value = true

	const promise = api.post("personal/users/relation-requests", {
		team_id: team.value.id,
		message: message.value
	})

	promise.then((response) => {
		onDialogOK(response.data)
	})

	promise.catch((error) => {
		notifyError(error.response.data.message)
	})

	promise.finally(() => {
		isLoading.value = false
	})
}
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isMobileWidthThreshold"
		transition-show="slide-up"
		transition-hide="slide-down"
	>
		<q-card class="q-dialog-plugin column q-pa-md">
			<div
				v-if="isMobileWidthThreshold"
				class="col-auto q-mb-lg text-right"
			>
				<q-icon
					v-close-popup
					name="close"
					size="md"
					class="cursor-pointer"
				/>
			</div>
			<div class="col-auto q-pa-md q-mb-md text-center bg-primary text-white text-body1">
				Присоединиться к существующей команде.<br/>
				Вы по-прежнему сможете зарегистрировать собственную.
			</div>
			<q-form
				class="col column"
				@submit="submitRelationRequest"
			>
				<div class="col-auto q-mb-md">
					<q-select
						filled
						v-model="team"
						use-input
						clearable
						input-debounce="300"
						label="Начните вводить название компании *"
						:options="teamList"
						option-value="id"
						option-label="label"
						:disable="isLoading"
						@filter="loadTeamList"
						lazy-rules
						:rules="[
							val => !!val,
						]"
					>
						<template v-slot:no-option>
							<q-item>
								<q-item-section class="text-grey">
									Компания не найдена
								</q-item-section>
							</q-item>
						</template>
					</q-select>

					<!-- todo - maxlength -->
					<q-input
						v-model="message"
						filled
						type="textarea"
						label="Добавьте текст заявки (необязательно)"
						:disable="isLoading"
					/>
				</div>
				<div class="col content-end">
					<q-btn
						label="Подтвердить"
						color="primary"
						class="q-pa-md full-width"
						type="submit"
						:loading="isLoading"
					/>
				</div>
			</q-form>
		</q-card>
	</q-dialog>
</template>
