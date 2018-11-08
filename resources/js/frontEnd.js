/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.swal = require('sweetalert2');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Tickets from "./components/frontEnd/Tickets"

import "select2/dist/css/select2.min.css"

require('select2')

const app = new Vue({
                      el        : '#registration',
                      components: {
                        Tickets
                      },
                      data      : {
                        selectedTicket: null
                      },
                      computed  : {
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
                        $('.select2').select2();
                      },
                      methods   : {
                        update(ticket) {
                          this.selectedTicket = ticket
                        },
                        charge() {
                          if (!this.checkCUIPGStatus()) {return}

                        },
                        checkCUIPGStatus() {
                          swal('Error', 'Payment has some problem, please try again later')

                        }
                      }
                    });
