import GitSection from './components/GitSection.vue'
import GitView from './components/GitView.vue'

panel.plugin('oblik/git', {
	sections: {
		git: GitSection
	},
	views: {
		git: {
			label: 'Git',
			icon: 'box',
			component: GitView
		}
	}
})
