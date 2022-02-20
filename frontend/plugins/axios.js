import * as axios from 'axios'
axios.defaults.withCredentials = true;
let options = {}

// The server-side needs a full url to works
options.baseURL = process.env.HOST_URL
options.withCredentials = true
export default axios.create(options)
