<template>
    <table class="table">
        <thead>
        <th v-for="(header, index) in headers"
            :key="`head_${index}`"
            v-text="header"></th>
        </thead>
        <tbody>
        <tr v-for="(item, i) in items"
            :key="`item_${i}`">
            <td v-for="(key, j) in keys"
                :key="`item_content_${j}`"
                v-text="getContent(item, key)"></td>
            <td v-if="hasAction">
                <slot :item="item"
                      :deleteItem="deleteItem"></slot>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
  name   : "BasicTable",
  props  : {
    headers     : Array,
    keys        : Array,
    items       : Array,
    hasAction   : {
      type   : Boolean,
      default: false
    },
    deleteAction: Function
  },
  methods: {
    getContent(item, key) {
      return _.get(item, key)
    },
    deleteItem(item) {
      if (this.deleteAction) {
        this.deleteAction(item)
      } else {
        this.items = this.items.filter(i => i.id !== item.id)
      }


    }
  }
}
</script>