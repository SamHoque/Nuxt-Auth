<template>
  <div>
    <div class="has-text-centered" style="margin-top: 20px">
      <p>Logging you out...</p>
    </div>
    <Footer />
  </div>
</template>

<script>
import axios from '~/plugins/axios'
import Footer from "~/components/Footer";

export default {
  components: {Footer},
  data() {
    return {}
  },
  async fetch() {
    try {
      await axios.get('/logout');
      this.$toast.success('Logged out successfully').goAway(3500);
      await this.$router.push('/')
    } catch (e) {
      console.log(e.toString())
      this.$toast.error(e.toString()).goAway(3500);
    }
  }
}
</script>