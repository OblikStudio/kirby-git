<template>
	<section class="area-git-changes-list">
		<header class="k-section-header">
			<k-headline>{{ title }}</k-headline>
			<slot name="action"></slot>
		</header>

		<k-items v-if="entries" :items="entries" />
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
			type: String,
		},
		data: {
			type: Array,
		},
	},
	data() {
		return {
			perPage: 15,
			pageIdx: 0,
		};
	},
	computed: {
		pages() {
			return this.data.reduce((acc, val, i) => {
				let idx = Math.floor(i / this.perPage);
				let page = acc[idx] || (acc[idx] = []);
				page.push(val);

				return acc;
			}, []);
		},
		page() {
			if (!this.pages[this.pageIdx]) {
				this.pageIdx = 0;
			}

			return this.pages[this.pageIdx];
		},
		entries() {
			if (!this.page) {
				return null;
			}

			return this.page.map((entry) => {
				let image = {
					back: "black",
					icon: "question",
					color: "var(--color-gray-800)",
				};

				switch (entry.mode) {
					case "?":
					case "A":
						image.back = "var(--color-positive)";
						image.icon = "copy";
						break;
					case "M":
						image.back = "var(--color-notice)";
						image.icon = "edit";
						break;
					case "R":
						image.back = "var(--color-notice)";
						image.icon = "refresh";
						break;
					case "D":
						image.back = "var(--color-negative)";
						image.icon = "trash";
				}

				return {
					text: entry.file,
					image,
				};
			});
		},
	},
	methods: {
		changePage(data) {
			this.pageIdx = data.page - 1;
		},
	},
};
</script>
