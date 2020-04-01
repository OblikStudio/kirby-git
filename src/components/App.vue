<template>
  <section>
    <k-headline>{{ headline }}</k-headline>
  </section>
</template>

<script>
export default {
  data: () => {
    return {
      headline: null
    }
  },
  created () {
    this.load().then((response) => {
      this.headline = response.headline
    })

    this.$api.get('git/status').then((res) => {
      console.log(res)
    })
  }
}
</script>
