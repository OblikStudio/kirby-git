<template>
	<k-view>
		<k-header>Version Control</k-header>

		<k-grid gutter="medium">
			<k-column width="1/3">
				<changes-list title="Unstaged" :data="this.unstaged">
					<k-button-group v-if="this.unstaged.length" slot="action">
						<k-button icon="add" @click="add">Add</k-button>
					</k-button-group>
				</changes-list>
			</k-column>

			<k-column width="1/3">
				<changes-list title="Staged" :data="this.staged">
					<k-button-group v-if="this.staged.length" slot="action">
						<k-button icon="circle-filled" @click="$refs.commitDialog.open()">Commit</k-button>
					</k-button-group>
				</changes-list>

				<k-dialog
					ref="commitDialog"
					theme="positive"
					@submit="$refs.commitForm.submit()"
				>
					<k-form
						ref="commitForm"
						v-model="commitData"
						:fields="{
							message: {
								type: 'text',
								label: 'Message',
								required: true
							}
						}"
						@submit="commit"
					/>
				</k-dialog>
			</k-column>

			<k-column width="1/3">
				<commits-list
					:data="logData"
					@paginate="paginateLog"
				></commits-list>
			</k-column>
		</k-grid>

	</k-view>
</template>

<script>
import ChangesList from './ChangesList.vue'
import CommitsList from './CommitsList.vue'

export default {
	components: {
		ChangesList,
		CommitsList
	},
	data () {
		return {
			staged: [],
			unstaged: [],
			commitData: {
				message: null
			},
			logData: null,
			logPgn: null
		}
	},
	created () {
		this.$api.get('git/status').then(entries => {
			this.updateStatus(entries)
		})
	},
	methods: {
		updateStatus (entries) {
			this.staged = []
			this.unstaged = []

			entries.forEach((entry) => {
				if (entry.unstaged) {
					this.unstaged.push({
						file: entry.file,
						mode: entry.unstaged
					})
				}

				if (entry.staged && entry.staged !== '?') {
					this.staged.push({
						file: entry.file,
						mode: entry.staged
					})
				}
			})
		},
		add () {
			this.$api.post('git/add').then(() => {
				return this.$api.get('git/status')
			}).then(entries => {
				this.updateStatus(entries)
			})
		},
		commit () {
			this.$api.post('git/commit', this.commitData).then(() => {
				this.$refs.commitDialog.close()
				return this.$api.get('git/status')
			}).then(entries => {
				this.commitData.message = null
				this.updateStatus(entries)
			}).catch(error => {
				this.$refs.commitDialog.error(error.message)
			}).then(() => {
				this.listCommits()
			})
		},
		paginateLog (data) {
			this.logPgn = {
				page: data.page,
				limit: data.limit
			}

			this.listCommits()
		},
		listCommits (data) {
			this.$api.get('git/log', this.logPgn).then(data => {
				this.logData = data
			})
		}
	}
}
</script>
