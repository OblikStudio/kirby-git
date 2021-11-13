import GitSection from './components/GitSection.vue'
import GitView from './components/GitView.vue'

panel.plugin('oblik/git', {
	sections: {
		git: GitSection
	},
	components: {
		'k-git-view': GitView
	}
})
