<template>
    <fieldset class="ticket">
        <div class="form-group">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-outline-primary"
                     :class="{'active':selectType==='practitioner'}"
                     @click="changeType('practitioner')">
                <input type="radio" value="practitioner" v-model="selectType"> Medical Practitioners
              </label>
              <label class="btn btn-outline-primary"
                     :class="{'active':selectType==='trainee'}"
                     @click="changeType('trainee')">
                <input type="radio" value="trainee" v-model="selectType"> Para-medics / Trainees
              </label>
            </div>
        </div>

        <legend>Tickets:</legend>
         <div class="form-group row" v-for="ticket in tickets"
              v-if="showTicket(ticket)">
             <div class="col-sm-12">
                  <div class="form-group">
                     <div class="form-check form-check-inline">
                        <label style="margin-right: 15px">
                            <input type="radio" name="ticket_id"
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
  name   : "Tickets",
  props  : {
    tickets: {
      type    : Array,
      required: true
    }
  },
  data() {
    return {
      selectType: 'practitioner'
    }
  },
  methods: {
    showTicket(ticket) {
      return ticket.note === 'trainee' ?
             this.selectType === "trainee" :
             this.selectType === "practitioner"
    },
    changeType(type) {
      this.selectType = type
      this.$emit('select', type)
    }
  }
}
</script>

<style scoped>

</style>