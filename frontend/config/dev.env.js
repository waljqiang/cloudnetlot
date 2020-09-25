'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')
const projectConfig = require('../config/projectConfig')
module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  aaaa:'"dist/env.js"'
//  BASE_API: '"https://easy-mock.com/mock/5950a2419adc231f356a6636/vue-admin"',
})
