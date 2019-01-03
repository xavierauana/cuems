<template>
    <ul role="navigation" class="pagination">
        <li class="page-item">
            <a :href="prevLink"
               rel="prev"
               @click.prevent="$emit('go', currentPage>1?currentPage-1:currentPage)"
               aria-label="« Previous"
               class="page-link">‹</a>
        </li>
        <li class="page-item" v-for="n in totalPages"
            :class="{'active': currentPage === n }">
            <a v-if="currentPage !== n"
               :href="url+'?page='+n"
               class="page-link"
               @click.prevent="$emit('go', n)"
            >{{n}}</a>
            <span v-else class="page-link">{{n}}</span>
        </li>
        <li class="page-item">
        <a :href="nextLink"
           @click.prevent="$emit('go', currentPage<totalPages?currentPage+1:currentPage)"
           rel="next"
           aria-label="Next »"
           class="page-link">›</a>
        </li>
    </ul>
</template>

<script>
export default {
  name   : "BasicPaginationNav",
  props  : {
    baseUrl    : {
      type    : String,
      required: true,
    },
    currentPage: Number,
    prevLink   : String,
    nextLink   : String,
    totalPages : Number
  },
  data() {
    return {
      url: window.origin + window.location.pathname
    }
  },
  methods: {
    getUrl(index) {
      return this.baseUrl + 'page=' + index
    }
  }
}
</script>

<style scoped>

</style>