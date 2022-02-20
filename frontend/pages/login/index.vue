<template>
  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column is-4 is-offset-4">
          <h2 class="title has-text-centered">Login!</h2>

          <Notification :message="error" v-if="error"/>

          <form method="post" @submit.prevent="login">
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
              <button type="submit" class="button is-dark is-fullwidth">Login</button>
            </div>
          </form>

          <div class="has-text-centered" style="margin-top: 20px">
             Don't have an account yet? <nuxt-link to="/register">Register</nuxt-link>
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
    async login() {
      try {
        this.$toast.show('Logging in...').goAway(3500);
        let {data} = await axios.post('/login', JSON.stringify({
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
          this.$toast.success('Logged in successfully').goAway(3500);
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