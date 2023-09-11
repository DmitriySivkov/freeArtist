<template>
	<q-responsive :ratio="16/9">
		<q-card>
			<q-skeleton class="fit" />
		</q-card>
	</q-responsive>
	<div class="column fit">
		<q-card class="col-grow">
			<div class="row">
				<q-card class="col-12 full-height">
					<q-card-section>
						<span class="text-h5">...</span>
					</q-card-section>
					<q-card-section
						v-for="n in 3"
						:key="n"
						class="q-py-xl q-hoverable"
					>
						<span class="q-focus-helper"></span>
						<q-separator inset />
					</q-card-section>
				</q-card>
			</div>
			<q-separator inset />
		</q-card>
	</div>
</template>
