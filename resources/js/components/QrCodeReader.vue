<template>
  <div>
    <video muted playsinline ref="video" id="qrCodeReaderViewer"></video>
      <!-- Modal -->
<div class="modal fade" id="cameraSelectionModal" tabindex="-1" role="dialog"
     aria-labelledby="cameraSelectionModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Select Camera</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p v-for="(camera,index) in cameras" :key="index">{{camera.name}} <button
                class="btn btn-info btn-sm float-right text-light"
                @click.prevent='pickCamera(index)'>Pick</button></p>
      </div>
    </div>
  </div>
</div>
  </div>
</template>

<script>
import Instascan from "instascan-last";

export default {
  data() {
    return {
      content: null,
      cameras: [],
      camera : null,
      scanner: null,
    };
  },
  watch  : {
    camera(newValue, oldValue) {
      console.log(newValue, oldValue)
      this.scanner.start(newValue);
    }
  },
  mounted() {
    const video = this.$refs.video;
    this.scanner = new Instascan.Scanner({
                                           video           : video,
                                           scanPeriod      : 5,
                                           refractoryPeriod: 5000
                                         });
    
    console.log(this.scanner)
    this.scanner.addListener("scan", (content, image) => this.$emit("scan", content));
    this.scanner.addListener("active", () => console.log("scanner is active"));
    this.scanner.addListener("inactive", () => console.log("scanner is inactive"));

    if (navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({video: true}).then(() => {
        Instascan.Camera.getCameras()
                 .then(cameras => {
                   if (cameras.length > 0) {
                     this.cameras = cameras
                     this.selectCamera()
                   } else {
                     alert("No cameras found.");
                   }
                 })
                 .catch(e => {
                   alert('cannot initialized camera.')
                   console.error(e)
                 });
      });
    }
  },
  methods: {
    selectCamera() {
      $("#cameraSelectionModal").modal('show')
    },
    pickCamera(index) {
      this.camera = this.cameras[index]
      this.$refs.video.style.transform = ""
      $("#cameraSelectionModal").modal('hide')
    }
  }
};
</script>

<style scoped>
video {
    width: 100%;
    background-color: gray;
}
</style>
