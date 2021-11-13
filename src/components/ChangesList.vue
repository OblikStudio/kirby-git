<template>
	<section class="area-git-changes-list">
		<header class="k-section-header">
			<k-headline>{{ title }}</k-headline>
			<slot name="action"></slot>
		</header>

		<k-items v-if="entries" :layout="list">
			<k-item
				v-for="entry in entries"
				:key="entry.file"
				:text="entry.file"
				:image="{ back: 'none', icon: entry.icon }"
			></k-item>
		</k-items>
		<template v-else>
			<k-empty icon="check">No changes</k-empty>
		</template>

		<k-pagination
			align="center"
			:details="true"
			:page="pageIdx + 1"
			:total="data.length"
			:limit="perPage"
			@paginate="changePage"
		/>
	</section>
</template>

<script>
export default {
	props: {
		title: {
			type: String
		},
		data: {
			type: Array
		}
	},
	data () {
		return {
			perPage: 15,
			pageIdx: 0
		}
	},
	computed: {
		pages () {
			return this.data.reduce((acc, val, i) => {
				let idx = Math.floor(i / this.perPage)
				let page = acc[idx] || (acc[idx] = [])
				page.push(val)

				return acc
			}, [])
		},
		page () {
			if (!this.pages[this.pageIdx]) {
				this.pageIdx = 0
			}

			return this.pages[this.pageIdx]
		},
		entries () {
			if (this.page) {
				return this.page.map(entry => {
					return {
						file: entry.file,
						mode: entry.mode,
						icon: this.getIcon(entry.mode)
					}
				})
			} else {
				return null
			}
		}
	},
	methods: {
		changePage (data) {
			this.pageIdx = data.page - 1
		},
		getIcon (mode) {
			let icon = 'dots'

			switch (mode) {
				case '?':
				case 'A':
					icon = 'copy'
					break
				case 'M':
					icon = 'edit'
					break
				case 'R':
					icon = 'refresh'
					break
				case 'D':
					icon = 'trash'
			}

			return icon
		}
	}
};
</script>

<style lang="scss">
.area-git-changes-list {
	.k-icon {
		color: var(--color-gray-900);
	}

	.k-icon-copy {
		background: var(--color-positive);
	}

	.k-icon-edit,
	.k-icon-refresh {
		background: var(--color-notice);
	}

	.k-icon-trash {
		background: var(--color-negative);
	}
}
</style>
