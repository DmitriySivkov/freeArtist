<template>
	<div class="column full-height q-gutter-xs">
		<q-card
			v-for="n in 3"
			:key="n"
			class="col"
		>
			<q-skeleton class="fit" />
		</q-card>
	</div>
</template>
