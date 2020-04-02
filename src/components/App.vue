<template>
  <section>
    <k-headline>
      {{ headline }}

      <template v-if="stats.total">
        ({{ stats.total }} files)
      </template>
    </k-headline>

    <k-box v-if="stats.added && stats.added.total" theme="positive">
      {{ stats.added.total }} added

      <template v-if="stats.added.unstaged">
        ({{ stats.added.unstaged }} unstaged)
      </template>
    </k-box>

    <k-box v-if="stats.modified && stats.modified.total">
      {{ stats.modified.total }} modified
      
      <template v-if="stats.modified.unstaged">
        ({{ stats.modified.unstaged }} unstaged)
      </template>
    </k-box>

    <k-box v-if="stats.deleted && stats.deleted.total" theme="negative">
      {{ stats.deleted.total }} deleted
      
      <template v-if="stats.deleted.unstaged">
        ({{ stats.deleted.unstaged }} unstaged)
      </template>
    </k-box>
  </section>
</template>

<script>
function resolve (files, type) {
  return files.reduce((memo, val) => {
    if (val.staged === type) {
      memo.staged++
      memo.total++
    }

    if (val.unstaged === type || (type === 'A' && val.unstaged === '?')) {
      memo.unstaged++
      memo.total++
    }

    return memo
  }, {
    total: 0,
    staged: 0,
    unstaged: 0
  })
}

export default {
  data: () => {
    return {
      headline: null,
      stats: {
        total: null,
        added: null,
        modified: null,
        deleted: null
      }
    }
  },
  created () {
    this.load().then((response) => {
      this.headline = response.headline
    })

    this.$api.get('git/status').then((res) => {
      if (res.files) {
        this.stats.total = res.files.length
        this.stats.added = resolve(res.files, 'A')
        this.stats.modified = resolve(res.files, 'M')
        this.stats.deleted = resolve(res.files, 'D')
      }
    })
  }
}
</script>
