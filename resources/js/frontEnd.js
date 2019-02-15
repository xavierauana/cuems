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
                        isTraineeTicket: false
                      },
                      mounted() {
                        $('div#other_position_container').hide()
                        $('.select2').select2();
                        $('.select2-tag').select2({
                                                    tags: true
                                                  });
                        $('.other_institution_container').hide();
                        $('.select2[name=institution]').on('select2:close', e => {
                          if (e.target.value === 'Others') {
                            $('.other_institution_container').show();
                            $('input[name=other_institution]').attr('require', true)
                          } else {
                            $('.other_institution_container').hide();
                            $('input[name=other_institution]').attr('require', false)
                          }
                        })

                        $('select[name="position"]').on('change', function (e) {
                          const val = e.target.value,
                                $el = $('div#other_position_container')

                          if (val === "Others") {
                            $el.show()
                          } else {
                            $el.hide()
                          }
                        })


                      },
                      methods   :  {
                        update(type) {
                          this.isTraineeTicket = type === 'trainee'
                          if (type === 'trainee') {
                            Vue.nextTick(() => {
                              $('.select2').select2();
                            });
                          }
                        },
                        charge() {
                          if (!this.checkCUIPGStatus()) {return}
                        },
                        checkCUIPGStatus() {
                          swal('Error', 'Payment has some problem, please try again later')
                        }
                      }
                    });
