<template>
	<section>
		<header class="k-section-header">
			<k-headline>{{ finalHeadline }}</k-headline>
		</header>

		<k-box v-if="positiveStatus" :text="positiveStatus" theme="positive"></k-box>
		<k-box v-if="noticeStatus" :text="noticeStatus" theme="notice"></k-box>
		<k-box v-if="negativeStatus" :text="negativeStatus" theme="negative"></k-box>
	</section>
</template>

<script>
function getStats () {
	return {
		total: 0,
		added: 0,
		untracked: 0,
		modified: 0,
		renamed: 0,
		deleted: 0
	}
}

export default {
	data: () => {
		return {
			headline: null,
			stats: getStats()
		}
	},
	computed: {
		finalHeadline () {
			let text = this.headline

			if (this.stats.total) {
				text += ` (${ this.stats.total } changes)`
			}

			return text
		},
		positiveStatus () {
			let text = []

			if (this.stats.added) {
				text.push(`${ this.stats.added } added`)
			}

			if (this.stats.untracked) {
				text.push(`${ this.stats.untracked } untracked`)
			}

			if (text.length) {
				return text.join(', ')
			} else {
				return null
			}
		},
		noticeStatus () {
			let text = []

			if (this.stats.modified) {
				text.push(`${ this.stats.modified } modified`)
			}

			if (this.stats.renamed) {
				text.push(`${ this.stats.renamed } renamed`)
			}

			if (text.length) {
				return text.join(', ')
			} else {
				return null
			}
		},
		negativeStatus () {
			if (this.stats.deleted) {
				return `${ this.stats.deleted } deleted`
			} else {
				return null
			}
		}
	},
	created () {
		this.load().then((response) => {
			this.headline = response.headline

			this.$api.get('git/status').then((entries) => {
				this.stats = getStats()

				if (entries.length) {
					this.updateStats(entries)
				}
			})
		})
	},
	methods: {
		updateStats (entries) {
			this.stats.total = entries.length

			entries.forEach((entry) => {
				let status = entry.staged || entry.unstaged

				switch (status) {
					case 'A':
						this.stats.added++
						break
					case '?':
						this.stats.untracked++
						break
					case 'M':
						this.stats.modified++
						break
					case 'R':
						this.stats.renamed++
						break
					case 'D':
						this.stats.deleted++
				}
			})
		}
	}
}
</script>
