<template>
	<section>
		<header class="k-section-header">
			<k-headline>Commits</k-headline>
			<slot name="action"></slot>
		</header>

		<k-items v-if="data && data.commits.length">
			<k-item
				v-for="commit in data.commits"
				:key="commit.hash"
				:text="commit.subject"
				:info="commit.hash"
				:image="{
					back: commit.new ? 'green' : 'black',
					icon: commit.new ? 'upload' : 'circle-filled',
					color: commit.new ? 'gray-800' : 'gray-500',
				}"
			></k-item>
		</k-items>
		<template v-else>
			<k-empty icon="circle-filled">No commits</k-empty>
		</template>

		<k-pagination
			v-if="data"
			align="center"
			:details="true"
			:page="page"
			:total="data.total"
			:limit="limit"
			v-on="$listeners"
		/>
	</section>
</template>

<script>
export default {
	props: {
		data: Object,
	},
	data() {
		return {
			page: 1,
			limit: 15,
		};
	},
	created() {
		this.$emit("paginate", {
			page: this.page,
			limit: this.limit,
		});
	},
};
</script>

<style scoped>
.k-item >>> .k-item-info {
	font-family: var(--font-mono);
}
</style>
