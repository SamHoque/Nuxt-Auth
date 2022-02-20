<template>
  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column is-4 is-offset-4">
          <h2 class="title has-text-centered">Register!</h2>

          <form method="post" @submit.prevent="register">
            <div class="field">
              <label class="label">Username</label>
              <div class="control">
                <input
                    type="text"
                    class="input"
                    name="username"
                    v-model="username"
                    required
                />
              </div>
            </div>
            <div class="field">
              <label class="label">Password</label>
              <div class="control">
                <input
                    type="password"
                    class="input"
                    name="password"
                    v-model="password"
                    required
                />
              </div>
            </div>
            <div class="control">
              <button type="submit" class="button is-dark is-fullwidth">Register</button>
            </div>
          </form>

          <div class="has-text-centered" style="margin-top: 20px">
            Already got an account?
            <nuxt-link to="/login">Login</nuxt-link>
          </div>
          <div class="has-text-centered" style="margin-top: 20px">
            <nuxt-link to="/">Home</nuxt-link>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </section>
</template>

<script>
import axios from '~/plugins/axios'
import Footer from "~/components/Footer";

export default {
  components: {Footer},
  data() {
    return {
      username: '',
      password: '',
      error: null
    }
  },

  methods: {
    async register() {
      try {
        this.$toast.show('Registering in...').goAway(3500);
        let {data} = await axios.post('/register', JSON.stringify({
          username: this.username,
          password: this.password
        }), {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        });

        console.log(data);
        if(data.success) {
          console.log(data.parsed);
          this.$toast.success('Registered successfully').goAway(3500);
        } else {
          this.$toast.error(data.error).goAway(3500);
        }
        await this.$router.push('/')
      } catch (e) {
        this.$toast.error(e.toString()).goAway(3500);
      }
    }
  }
}
</script>