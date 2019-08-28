// import '@babel/polyfill'
// import 'isomorphic-fetch'

import React from 'react'
import { render } from 'react-dom'

// import './style'
// import '~components/Footer/src/components/style'

import ready from '~components/Helper/src/components/ready'
// import Nav from '~components/Nav/src/components'
// import ServerStatus from '~components/ServerStatus/src/components'
// import NetworkStats from '~components/NetworkStats/src/components'
// import ServerInfo from '~components/ServerInfo/src/components'
// import ServerBenchmark from '~components/ServerBenchmark/src/components'
// import Updater from '~components/Updater/src/components'
// import MyInfo from '~components/MyInfo/src/components'
import styled from 'styled-components'
import Title from '~components/Title/src/components'
import Container from '~components/Container/src/components'
// import Row from '~components/Grid/src/components/row'
// import Grid from '~components/Grid/src/components/grid'
import Cards from '~components/Card/src/components'
import Normalize from './style'
import '~components/ServerStatus/src'
import '~components/ServerInfo/src'
import '~components/PhpInfo/src'
import '~components/PhpExtensions/src'
import '~components/Database/src'
import '~components/MyInfo/src'

const App = styled.div`
  position: fixed;
  top: 1rem;
  left: 1rem;
  right: 1rem;
  bottom: 3rem;
  border-radius: 2rem;
  background: #f8f8f8;
  padding-top: 3rem;
  overflow-y: scroll;
  scroll-behavior: smooth;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
  will-change: transform;
`
const Bootstrap = () => (
  <>
    <Normalize />
    <Title />
    <App>
      <Container>
        <Cards />
      </Container>
    </App>
    {/* <Nav />
    <ServerStatus />
    <NetworkStats />
    <ServerInfo />
    <ServerBenchmark />
    <Updater />
    <MyInfo /> */}
  </>
)

ready(() => {
  const c = document.createElement('div')
  document.body.appendChild(c)

  render(<Bootstrap />, c)
})
