/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.swal = require('sweetalert2');
window.Dropzone = require('dropzone');
require('select2')

import flatpickr from "flatpickr"
import request from "./request"


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('delete-button', require('./components/basic/BasicDeleteButton'));
Vue.component('b-table', require('./components/basic/BasicTable'));
Vue.component('p-table', require('./components/PaginatedTable'));
Vue.component('tickets', require('./components/frontEnd/Tickets'));

const app = new Vue({
                      el      : '#app',
                      data    : {
                        selectedTicket: null
                      },
                      computed: {
                        isTraineeTicket() {
                          if (this.selectedTicket) {
                            let check = this.selectedTicket.note.indexOf('trainee') > -1
                            if (check) {
                              Vue.nextTick(() => {
                                $('.select2').select2();
                              })
                            }
                            return check
                          }
                          return false
                        }
                      },
                      mounted() {
                        $(".select2").select2()
                        $(".select2-tag").select2({
                                                    tags: true
                                                  })
                        flatpickr(".date-time", {
                          enableTime: true,
                          dateFormat: "d M Y H:i"
                        })
                        flatpickr(".date", {
                          dateFormat: "d M Y"
                        })

                        this.registerSearchDelegateFields('email')
                        this.registerSearchDelegateFields('mobile')
                      },
                      methods : {
                        confirmDelete(e) {
                          if (confirm("Are you sure to delete the item?")) {
                            e.target.submit()
                          }
                        },
                        registerSearchDelegateFields(inputName) {
                          let el = document.querySelector('input.delegate-form[name="' + inputName + '"]')

                          if (el) {
                            el.addEventListener('change', () => {
                              request.searchDelegate(el)
                                     .then(data => console.log(data))
                                     .catch(error => console.log("this is error, ", error.response))

                            })
                          }
                        },
                        update(payload) {
                          this.selectedTicket = payload
                        }
                      }
                    })
