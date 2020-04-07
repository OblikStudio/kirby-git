<template>
	<k-view>
		<k-header>Version Control</k-header>

		<k-grid gutter="medium">
			<k-column width="1/3">
				<changes-list title="Unstaged" :data="this.unstaged" />
			</k-column>

			<k-column width="1/3">
				<changes-list title="Staged" :data="this.staged" />
			</k-column>

			<k-column width="1/3">
				<k-headline>Commits</k-headline>
			</k-column>
		</k-grid>

	</k-view>
</template>

<script>
import ChangesList from './ChangesList.vue'

export default {
	components: {
		ChangesList
	},
	data () {
		return {
			staged: [],
			unstaged: []
		}
	},
	created () {
		this.$api.get('git/status').then((entries) => {
			entries.forEach((entry) => {
				if (entry.unstaged) {
					this.unstaged.push({
						file: entry.file,
						mode: entry.unstaged
					})
				}

				if (entry.staged) {
					this.staged.push({
						file: entry.file,
						mode: entry.staged
					})
				}
			})
		})
	}
}
</script>
