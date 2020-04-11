<template>
	<section>
		<header class="k-section-header">
			<k-headline>Commits</k-headline>
			<slot name="action"></slot>
		</header>

		<k-list v-if="data && commits.length">
			<k-list-item
				v-for="commit in commits"
				:key="commit.hash"
				:text="commit.subject"
				:info="commit.hash"
				:icon="{ type: commit.icon, back: 'black' }"
			></k-list-item>
		</k-list>
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
		data: Object
	},
	data () {
		return {
			page: 1,
			limit: 15
		}
	},
	computed: {
		commits () {
			return this.data.commits.map(commit => {
				let icon = commit.new
					? 'upload'
					: 'circle-filled'

				return {
					...commit,
					icon
				}
			})
		}
	},
	created () {
		this.$emit('paginate', {
			page: this.page,
			limit: this.limit
		})
	}
}
</script>

<style scoped>
.k-list-item >>> .k-icon-upload {
	background: var(--color-positive);
	color: black;
}
</style>
