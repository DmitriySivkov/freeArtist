<script setup>
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { computed, ref } from "vue"

const user_store = useUserStore()
const producer_store = useProducerStore()

const options = [
	{ label: "Рядом с вами", value: 1 },
	{ label: "Все", value: 2 }
]

const selected_option = ref(
	options.find((o) => o.value === 1).label
)

const setLocationRange = (range) => {
	selected_option.value = range.label
	user_store.setLocationRange(range.value)
}

const is_fetching_producers = computed(() => producer_store.is_fetching)
</script>

<template>
	<div class="row items-center justify-end">
		<div class="col-xs-12 col-md-4">
			<q-select
				:disable="is_fetching_producers"
				filled
				square
				:model-value="selected_option"
				:options="options"
				bg-color="white"
				@update:model-value="setLocationRange"
			>
				<!--							<template v-slot:no-option>-->
				<!--								<q-item>-->
				<!--									<q-item-section class="text-grey">-->
				<!--										Город не найден-->
				<!--									</q-item-section>-->
				<!--								</q-item>-->
				<!--							</template>-->
			</q-select>
		</div>
	</div>
</template>
