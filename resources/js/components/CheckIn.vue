<template>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="input-group mb-3">
              <input type="text" class="form-control form-control-lg"
                     placeholder="Delegate registration id, email, or phone"
                     aria-label="Recipient's username"
                     v-model='keyword'
                     aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-lg btn-info text-light"
                        @click.prevent='manualSearch'>Search</button>
              </div>
            </div>

        </div>
         <div class="col-md-4 col-lg-3">
		        <qr-reader v-on:scan="getDelegate"></qr-reader>
             <button class="btn btn-block btn-info text-light"
                     @click.prevent="flipVideo">Flip</button>
	        </div>
        <div class="col">
            <h2>Delegate Info</h2>
            <section v-if='record' class=" h5">
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Registration Id:
                    </div>
                    <div class="col">
                        {{record.delegate.registration_id}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Name:
                    </div>
                    <div class="col">
                        {{record.delegate.name}}
                    </div>
                </div>
                 <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Email:
                    </div>
                    <div class="col">
                        {{record.delegate.email}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                       Mobile:
                    </div>
                    <div class="col">
                        {{record.delegate.mobile}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Role:
                    </div>
                    <div class="col">
                        {{record.delegate.role}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Institution:
                    </div>
                    <div class="col">
                        {{record.delegate.institution}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Department:
                    </div>
                    <div class="col">
                        {{record.delegate.department}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Position:
                    </div>
                    <div class="col">
                        {{record.delegate.position}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Sponsor:
                    </div>
                    <div class="col">
                        {{sponsorName}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 text-md-right font-weight-bold   ">
                        Ticket:
                    </div>
                    <div class="col">
                        {{record.ticket}}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12 font-weight-bold">
                        Check In Record:
                    </div>
                    <div class="col-12">
                        <ul class="list-unstyled">
                            <li v-for="checkInRecord in record.check_in">{{checkInRecord.timestamp}} - {{checkInRecord.user.name}}</li>
                        </ul>
                    </div>
                </div>

                <div class="clearfix">

                <button class="btn btn-lg btn-success" @click.prevent="checkIn">Check-In</button>
                <button class="btn btn-lg btn-primary float-right"
                        @click.prevent="reset">Reset</button>
                </div>
            </section>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="delegateSearchModal" tabindex="-1"
             role="dialog"
             aria-labelledby="delegateSearchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"
                    id="delegateSearchModalLabel">Delegates</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <ul class="list-unstyled">
                    <li v-for="delegate in searchResult" class="mb-3">
                        <p><strong>{{delegate.prefix}} {{delegate.first_name}} {{delegate.last_name}}</strong> <small>({{delegate.registration_id}})</small></p>
                        <ul class="list-unstyled">
                            <li v-for='transaction in delegate.transactions'>
                                {{transaction.ticket.name}}
                                <button class="btn btn-lg btn-info float-right text-light"
                                        @click.prevent="pickTransaction(transaction.uuid)">Pick</button>
                            </li>
                        </ul>
                    </li>
                </ul>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-secondary"
                        data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
    </div>

</template>

<script>
    import QrReader from "./QrCodeReader"
    import swal from "sweetalert2"
    import request from "../request.js"

    export default {
      name      : "CheckIn",
      props     : {
        eventId: {
          type    : Number,
          required: true
        }
      },
      data() {
        return {
          flipped     : false,
          record      : null,
          token       : null,
          keyword     : null,
          searchResult: []
        }
      },
      computed  : {
        sponsorName() {
          const sponsor = this.record.delegate.sponsor
          return sponsor ? sponsor.name : null
        }
      },
      components: {
        QrReader
      },
      methods   : {
        getDelegate(token) {
          this.token = token
          swal({
                 title            : "",
                 text             : "Loading...",
                 showConfirmButton: false
               })
          request.getDelegateByToken(this.eventId, token)
                 .then(response => this.record = response.data.data)
                 .catch(error => alert("cannot find record"))
                 .finally(() => swal.close());
        },
        checkIn() {
          swal({
                 title            : "",
                 text             : "Loading...",
                 showConfirmButton: false
               })
          request.checkIn(this.eventId, this.token)
                 .then(response => this.record.check_in.splice(0, 0, response.data.record))
                 .catch(error => alert("cannot check in"))
                 .finally(() => swal("Success"));
        },
        manualSearch() {
          request.checkInManualSearch(this.eventId, this.keyword)
                 .then(response => {
                   this.searchResult = response.data
                   $("#delegateSearchModal").modal('show')
                 })
        },
        pickTransaction(token) {
          $("#delegateSearchModal").modal('hide')
          this.getDelegate(token)
        },
        flipVideo() {
          let el = document.querySelector("#qrCodeReaderViewer")
          el.style.transform = el.style.transform.length > 0 ? "" : "scaleX(-1)"
        },
        reset() {
          this.token = null
          this.record = null
          this.keyword = null
          this.searchResult = []
        }
      }
    }
</script>

<style scoped>

</style>