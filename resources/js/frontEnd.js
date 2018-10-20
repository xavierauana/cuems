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

const app = new Vue({
                      el        : '#registration',
                      data      : {
                        selectedTicket: null
                      },
                      components: {
                        Tickets
                      },
                      watch     : {
                        selectedTicket(ticket) {
                          if (this.selectedTicket.note.indexOf("trainee") > -1) {
                            _.forEach(document.querySelectorAll("fieldset.trainee input, fieldset.trainee select"), item => item.setAttribute('required', true))
                          } else {
                            _.forEach(document.querySelectorAll("fieldset.trainee input, fieldset.trainee select"), item => item.removeAttribute('required'))
                          }
                        }
                      },
                      methods   : {
                        update(ticket) {
                          this.selectedTicket = ticket
                        }
                      }
                    });
