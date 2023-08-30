<script setup>
import { useQuasar } from "quasar"
import { computed } from "vue"

const $q = useQuasar()
const isWidthThreshold = computed(() =>
	$q.screen.width >= $q.screen.sizes.sm
)
</script>
<template>
	<div class="column no-wrap fit">
		<template v-if="isWidthThreshold">
			<q-card
				v-for="n in 2"
				:key="n"
				class="col-xs-9 col-md-6 q-mb-md"
			>
				<div class="column fit">
					<div class="col-xs-12 col-sm-9 row">
						<q-skeleton class="col-xs-12 col-sm-8" />
						<q-separator
							vertical
							color="black"
						/>
						<q-skeleton class="col-xs-12 col-sm-grow" />
					</div>
					<q-separator
						color="black"
					/>
					<div class="col-auto row">
					</div>
				</div>
			</q-card>
		</template>
		<template v-else>
			<q-card
				v-for="n in 2"
				:key="n"
				class="col-xs-12 col-sm-6 column no-wrap q-mb-md"
			>
				<q-skeleton
					class="col-9 row"
				/>
				<q-separator color="black" />
			</q-card>
		</template>
	</div>
</template>
