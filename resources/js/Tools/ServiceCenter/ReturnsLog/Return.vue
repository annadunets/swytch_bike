<template>
  <div class="mb-5"> <!-- keep this single "parent" div in your template -->

    <!-- your Blade layout here -->
    <check-in-details :return="this.return"></check-in-details>

    <testing-header v-if="this.return.id" :return="this.return" :saved-return="this.savedReturn" v-on:startProcessing="startProcessing" v-on:returnUpdate="returnUpdate" :service-center-id="this.serviceCenterId"></testing-header>
    
    <template v-if="this.savedReturn.tested_date">
      <testing-components v-if="this.return.id" :return="this.return" :service-center-id="this.serviceCenterId"></testing-components>
      <testing-footer v-if="this.return.id" :return="this.return" :service-center-id="this.serviceCenterId"></testing-footer>

      <div class="row mb-5">

        <template v-if="!this.return.completed_date">
          <button class="col m-1 btn btn-lg btn-success" @click="clickSaveAndComplete" :disabled="!completeViable">SAVE & COMPLETE</button>
          <button class="col m-1 btn btn-lg btn-warning" @click="clickSaveInProgress">SAVE IN PROGRESS</button>
        </template>
        <template v-else>
          <div class="card col-12 p-0">
          <div class="card-body m-0 pb-0 pt-3 alert-secondary">
            <date-form-entry :label="'Completed Date (' + this.return.service_center.timezone + ')'" :timezone="this.return.service_center.timezone"  :value="this.savedReturn.completed_date ? this.return.completed_date_localized_formatted : this.return.completed_date_localized" :isDisabled="true"/>
          </div>
          </div>
          <button class="col m-1 btn btn-lg btn-primary" @click="clickReturnToInProgress">SET TO IN PROGRESS</button>
        </template>
        <confirm-button class="col m-1" base-style="btn-warning" confirm-style="btn-danger" v-on:clickConfirmed="clickDiscard">DISCARD AND DELETE</confirm-button>
        <div v-if="Object.keys(this.form_errors).length" class="col-12 text-center mt-3 text-danger">
          <h3>Errors:</h3>
          <ul v-for="(errField, val) in this.form_errors" :key="errField">
            <li v-for="errMsg in errField" :key="errMsg">
            <strong>{{ val }}:</strong> {{ errMsg }}
            </li>
          </ul>
        </div>
      </div>
    </template>
    <template v-else>
        <confirm-button class="col m-1" base-style="btn-warning" confirm-style="btn-danger" v-on:clickConfirmed="clickDiscard">DISCARD AND DELETE</confirm-button></template>
  </div>
</template>

<script>
import CheckInDetails from "./ReturnComponents/CheckInDetails.vue"
import TestingHeader from './ReturnComponents/TestingHeader.vue'
import TestingComponents from './ReturnComponents/TestingComponents.vue'
import ConfirmButton from '../../../Components/ConfirmButton.vue'
import TestingFooter from './ReturnComponents/TestingFooter.vue'
import moment from 'moment'
import DateFormEntry from '../../../Components/Form/DateFormEntry.vue'

  export default 
  {
    components: { CheckInDetails, TestingHeader, TestingComponents, ConfirmButton, TestingFooter, DateFormEntry },
    props: {
      returnId: {
        required: true,
        type: Number
      },
      serviceCenterId: {
        required: true,
        type: Number
      } 
    },
    data () {
      return {
        return: {},
        savedReturn: {},
        form_errors: []
      }
    },
    created () {
      this.fetchReturn();
    },
    computed: {
      //Calcualtes wether completion is viable (e.g. checks/refund etc. are all filled out)
      completeViable() {
        if (!this.return.id) { return false } //if no return, don't attempt logic
        if (this.return.return_items.length === 0) { return false } //if no return items, not valid
        if (this.return.tested_date_localized === null) { return false }
        if (this.return.technician_id === null) { return false }
        if (this.return.all_checks === null) { return false } //need to set if all checks pass/fail
        if (this.return.refund_type === null) { return false }
        if (this.return.refund_type !== "not_applicable") {
          if (this.return.customer_aware === null) { return false }
        }
        return true
      }
    },  
    methods: {
      fetchReturn() {
        axios.get("/api/tools/servicecenter/" + this.serviceCenterId + "/returnslog/" + this.returnId)
          .then(({data}) => {
            this.return = data;
            this.savedReturn = {...data};

            if (this.return.tested_date_localized === null) {
              this.return.tested_date_localized = moment.tz(new Date(), moment.tz.guess()).tz(this.return.service_center.timezone).format("yyyy-MM-DD HH:mm:ss")
            }
        });
      },
      startProcessing() {
        this.return
        this.save()
        this.fetchReturn()
      },
      returnUpdate(newReturnValue) {
        this.return = newReturnValue
      },
      clickReturnToInProgress() {
        this.return.return_complete = false
        this.save()
        this.redirectToIndex(0.5);
      },
      clickSaveAndComplete() {
        this.return.return_complete = true
        this.save()
        this.redirectToIndex(0.5);
      },
      clickSaveInProgress() {
        this.return.return_complete = false
        this.save()
        this.redirectToIndex(0.5);
      },
      save() {
        axios.put("/api/tools/servicecenter/" + this.serviceCenterId + "/returnslog/" + this.return.id, this.return)
          .catch(error => {
            if (error.response.status === 422) {
              this.form_errors = error.response.data.errors || []
            }
            else {
              console.log(error)
            }
          });
        this.return.return_items.map((return_item) => {
          axios.put("/api/tools/servicecenter/" + this.serviceCenterId + "/returnslog/" + this.return.id + "/returnitem/" + return_item.id, return_item);
        });
      },
      clickDiscard() {
        axios.delete("/api/tools/servicecenter/" + this.serviceCenterId + "/returnslog/" + this.return.id)
          .then(() => {
            this.redirectToIndex(0.5);
        });
      },
      redirectToIndex(delaySeconds) {
        var serviceCenterId = this.serviceCenterId
        window.setTimeout(function(){
          window.location.href = "/tools/servicecenter/" + serviceCenterId + "/returnslog/index";
        }, delaySeconds * 1000);
      }
    }

  }
</script>
