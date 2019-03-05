<template>
  <div>
    <video muted playsinline ref="video"></video>
  </div>
</template>

<script>
import Instascan from "instascan-last";
export default {
  data() {
    return {
      content: null
    };
  },
  mounted() {
    const video = this.$refs.video;
    console.log(video);
    const scanner = new Instascan.Scanner({
      video: video,
      scanPeriod: 5,
      refractoryPeriod: 5000
    });
    scanner.addListener("scan", (content, image) => {
      console.log(content, image);
      this.$emit("scan", content);
    });
    scanner.addListener("active", () => console.log("scanner is active"));
    scanner.addListener("inactive", () => console.log("scanner is inactive"));

    if (navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({ video: true }).then(() => {
        Instascan.Camera.getCameras()
          .then(cameras => {
            if (cameras.length > 0) {
              scanner.start(cameras[0]);
            } else {
              console.error("No cameras found.");
            }
          })
          .catch(e => console.error(e));
      });
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
