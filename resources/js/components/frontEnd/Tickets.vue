<template>

    <fieldset class="ticket">
        <legend>Tickets:</legend>
         <div class="form-group row" v-for="ticket in tickets">
             <div class="col-sm-12">
                  <div class="form-group">
                     <div class="form-check form-check-inline">
                        <label style="margin-right: 15px">
                            <input type="radio" name="ticket_id"
                                   @change="$emit('select',ticket)"
                                   :value="ticket.id" /> {{ticket.name}} HK${{ticket.price}}
                        </label>
                     </div>
                </div>
             </div>
         </div>

        <slot name="errorMessage"></slot>
</fieldset>
</template>

<script>
export default {
  name : "Tickets",
  props: {
    tickets: {
      type    : Array,
      required: true
    }
  }
}
</script>

<style scoped>

</style>