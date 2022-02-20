<template>
  <div style="height: 100%">
    <div class="has-text-centered" style="margin-top: 20px">
      <h1>{{ status }}</h1>
      <p>Nuxt + Slim4.</p>

      <div v-if="status === 'Not Logged In'">
        <NuxtLink to="/login">Login</NuxtLink>
        |
        <NuxtLink to="/register">Register</NuxtLink>
      </div>
      <div v-else-if="status.indexOf('Welcome back') > -1">
        <NuxtLink to="/logout">Log out</NuxtLink>
      </div>
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
    return {
      status: 'Loading...'
    }
  },
  async fetch() {
    this.status = await axios.get('/').then(res => res.data);
  },
  fetchOnServer: false,
  head() {
    return {
      title: 'Nuxt Auth'
    }
  }
}
</script>

<style>
h1 {
  font-size: 40px;
}

p {
  font-size: 20px;
}
</style>
