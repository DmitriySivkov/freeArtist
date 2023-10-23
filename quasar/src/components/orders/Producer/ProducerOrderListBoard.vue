<template>
	<q-card
		class="col"
		:class="boardClass"
	>
		<div class="column full-height q-pb-xs q-ml-sm">
			<div class="col-shrink">
				<div class="row q-py-sm">
					<q-chip
						square
						:color="chipColor"
						text-color="white"
						class="q-ma-none"
					>
						{{ boardName }}
					</q-chip>
				</div>
			</div>
			<q-scroll-area class="col full-height">
				<div class="absolute fit column no-wrap">
					<!-- todo - stretch and make gutter -->
					<draggable
						:list="orders"
						group="orders"
						:sort="false"
						@start="drag=true"
						@end="drag=false"
						:item-key="(order) => order.id"
						class="row full-height q-gutter-xs"
						:component-data="{
							tag: 'div',
							type: 'transition-group',
							name: 'fade'
						}"
						v-bind="{
							animation: 200,
							group: 'orders',
							disabled: false,
							ghostClass
						}"
					>
						<template #item="{ element }">
							<q-card
								class="col-3 full-height flex flex-center cursor-pointer q-hoverable"
								:class="cardClass"
								@click="$emit('show', element)"
								style="max-height:40%"
							>
								<span class="q-focus-helper"></span>
								<div class="text-center">
									#{{ element.id }}
								</div>
							</q-card>
						</template>
					</draggable>
				</div>
			</q-scroll-area>

		</div>
	</q-card>
</template>

<script setup>
import { defineComponent, ref } from "vue"
import draggable from "vuedraggable"

defineComponent({
	draggable
})

defineEmits([
	"show"
])

const props = defineProps({
	boardName: String,
	orders: Array,
	boardClass: String,
	cardClass: String,
	chipColor: String,
	ghostClass: String,
})

const drag = ref(false)
</script>
