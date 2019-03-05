/**
 * Created by Xavier on 2019-02-18.
 */

import swal from 'sweetalert2'
import _ from 'lodash'

const isProduction = false;
const baseUrl = isProduction ? "/ems/" : "/"

export default {
  searchDelegate(el) {
    let name = el.getAttribute('name'),
        data = {}

    data[name] = el.value
    return axios.post(baseUrl + 'events/1/delegates/search', data)
                .then(response => {
                  const count = response.data.length
                  if (count > 0) {
                    let message = _.chain(response.data)
                                   .map(delegate => delegate.first_name + " " + delegate.last_name)
                                   .reduce((carry, name) => `${carry} ${name} <br/>`, "")
                                   .value()
                    swal('', 'There are ' + count + ' delegate with same ' + name + '<br/> ' + message)
                  }
                  return response
                })
  },
  getDelegateByToken(eventId, token) {
    return axios.get(baseUrl + 'events/' + eventId + '/checkin/' + token + '/delegate')
                .then(response => {
                  console.log(response)
                  return response
                })
  },
  checkIn(eventId, token) {
    return axios.post(baseUrl + 'events/' + eventId + '/checkin/' + token)
  },
  checkInManualSearch(eventId, keyword) {
    return axios.get(baseUrl + 'events/' + eventId + '/checkin/search?keyword=' + keyword)
  }
}