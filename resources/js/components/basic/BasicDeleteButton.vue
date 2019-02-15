<template>
    <button @click.prevent="deleteItem">
        <slot></slot>
    </button>
</template>

<script>
export default {
  name   : "BasicDeleteButton",
  props  : {
    url: {
      type    : String,
      required: true
    },
    id : {
      type    : String,
      required: true
    }
  },
  methods: {
    deleteItem() {
      swal({
             title             : 'Are you sure?',
             text              : "You won't be able to revert this!",
             type              : 'warning',
             showCancelButton  : true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor : '#d33',
             confirmButtonText : 'Yes, delete it!'
           })
        .then((result) => {
          if (result.value) {
            axios.delete(this.url)
                 .then(response => {
                   swal(
                     'Deleted!',
                     'Your file has been deleted.',
                     'success'
                   )
                   let el = document.querySelector(`tr[data-id='${this.id}']`)
                   if (el) {
                     el.remove()
                   }
                 })

          }
        })
    }
  }
}
</script>