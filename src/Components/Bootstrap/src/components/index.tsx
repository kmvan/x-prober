import '@babel/polyfill'
import 'isomorphic-fetch'

import React from 'react'
import { render } from 'react-dom'

import './style'
import '~components/Footer/src/components/style'

import ready from '~components/Helper/src/components/ready'
import Nav from '~components/Nav/src/components'
import ServerStatus from '~components/ServerStatus/src/components'
import NetworkStats from '~components/NetworkStats/src/components'
import ServerInfo from '~components/ServerInfo/src/components'
import ServerBenchmark from '~components/ServerBenchmark/src/components'
import Updater from '~components/Updater/src/components'
import MyInfo from '~components/MyInfo/src/components'

const App = () => (
  <>
    <Nav />
    <ServerStatus />
    <NetworkStats />
    <ServerInfo />
    <ServerBenchmark />
    <Updater />
    <MyInfo />
  </>
)

ready(() => {
  const c = document.createElement('div')
  document.body.appendChild(c)

  render(<App />, c)
})
