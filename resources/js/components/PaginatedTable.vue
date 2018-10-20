<template>
    <div class="paginated-table">
        <div class="row">
        <div class="col-sm-6">
        <div class="input-group m-3">
        <input type="text" class="form-control" placeholder="Find"
               aria-label="Find" aria-describedby="basic-addon2"
               v-model="filterWord">
        <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button"
                @click.prevent="filter">Filter</button>
        <button class="btn btn-outline-secondary" type="button"
                @click.prevent="all">Clear</button>
        </div>
        </div>
        </div>
        </div>

        <b-table :items="items"
                 :keys="keys"
                 :headers="headers"
                 :hasAction="hasAction"
                 :delete-action="deleteItem"
        >
            <template name="action"
                      slot-scope="{item, deleteItem}">
                <slot :item="item"
                      :deleteItem="deleteItem"></slot>
            </template>
        </b-table>

        <div class="m-3">
            <b-pagination-nav :baseUrl="baseUrl"
                              :current-page="iPaginator.current_page"
                              :pre-link="iPaginator.pre_page_url"
                              :next-link="iPaginator.next_page_url"
                              :total-pages="iPaginator.last_page"
                              @go="toPage"
            ></b-pagination-nav>
        </div>

    </div>

</template>

<script>
    import BTable from "./basic/BasicTable"
    import BPaginationNav from "./basic/BasicPaginationNav"

    export default {
      name      : "PaginatedTable",
      props     : {
        hasAction: {
          type    : Boolean,
          required: false
        },
        headers  : {
          type    : Array,
          required: true
        },
        keys     : {
          type    : Array,
          required: true
        },
        paginator: {
          type    : Object,
          required: true
        },
        searchUrl: String,
        allUrl   : {
          type   : String,
          require: true
        },
      },
      components: {
        BTable,
        BPaginationNav
      },
      data() {
        return {
          iPaginator: this.paginator,
          filterWord: "",
          queries   : []
        }
      },
      computed  : {
        items() {
          return this.iPaginator ? this.iPaginator['data'] : []
        },

        baseUrl() {
          return window.location.href.indexOf("page=") > -1 ? window.location.href.split("?")[0] + "?" : window.location.href + "?"
        }
      },
      mounted() {

        console.log(window.location)
        this.createQueries()

        let href        = window.location.href,
            string      = href.substring(href.indexOf("?") + 1, href.length) || "",
            queryArray  = string.split("&"),
            queryKeyVal = queryArray.map(item => item.split('='))
        queryKeyVal.forEach(KeyValPair => {
          if (KeyValPair[0] === 'keywords') {
            this.filterWord = KeyValPair[1]
          }
        })

        this.constructQueryString()
      },
      methods   : {
        deleteItem(item) {
          this.items = this.items.filter(i => i.id !== item.id)
        },
        filter() {
          if (this.searchUrl) {
            let url = this.searchUrl + "?keywords=" + this.filterWord
            axios.get(url)
                 .then(({data}) => {
                   this.iPaginator = data.institutions
                 })
          }
        },
        all() {
          window.location = this.allUrl
        },
        toPage(index) {
          this.alterPageIndex(index)
          let url = window.location.origin + window.location.pathname + this.constructQueryString()
          axios.get(url)
               .then(({data}) => {
                 console.log(data)
                 this.iPaginator = data.institutions
               })
        },
        createQueries() {
          if (window.location.search.length){
            this.queries = window.location.search.substring(1).split("&").map(pair => {
              let a = pair.split('=')
              return {key: a[0], value: a[1]}
            })
          }
        },
        constructQueryString() {

          let queryString = this.queries.reduce((carry, pair) => {
            return carry + pair.key + "=" + encodeURIComponent(pair.value) + "&"
          }, "?")

          if (queryString[queryString.length - 1] == "&") {
            queryString = queryString.substring(0, queryString.length - 1)
          }

          return queryString
        },
        alterPageIndex(index) {
          if (this.queries.filter(item => item.key === "page").length) {
            this.queries = this.queries.map(item => {
              if (item.key === "page") {
                item.value = index
              }
              return item
            })
          } else {
            this.queries.push({key: 'page', value: index})
          }
        }
      }
    }
</script>

<style scoped>

</style>