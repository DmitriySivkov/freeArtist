<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">

			<q-card class="q-mb-md">
				<q-card-section
					class="text-center bg-primary text-white text-body1"
				>
					Присоединиться к существующей команде.<br/>
					Вы по-прежнему сможете зарегистрировать собственную.
				</q-card-section>
			</q-card>

			<q-select
				filled
				v-model="team"
				use-input
				input-debounce="300"
				label="Начните вводить название компании *"
				:options="teams"
				option-value="id"
				option-label="label"
				@filter="loadTeamList"
				behavior="dialog"
				lazy-rules
				:rules="[
					val => !!val,
				]"
			/>

			<q-input
				v-model="message"
				filled
				type="textarea"
				label="Добавьте текст заявки (необязательно)"
			/>

			<div class="row q-mt-md">
				<div class="col-12">
					<q-btn
						label="Подтвердить"
						color="primary"
						class="q-pa-md full-width"
						@click="confirmRelationRequest"
					/>
				</div>
			</div>

		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"
import { ref } from "vue"
import { api } from "src/boot/axios"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const team = ref(null)
const message = ref("")

const teams = ref([])

const confirmRelationRequest = () => {
	onDialogOK({
		teamId: team.value.id,
		message: message.value
	})
}

const loadTeamList = async (query, update) => {
	if (query.length < 1) return

	const response = await api.get("personal/users/nonRelatedTeams",{
		params: { query }
	})

	update(() => {
		teams.value = response.data.map((team) => {
			return {
				label: team.display_name,
				id: team.id
			}
		})
	})
}
</script>
