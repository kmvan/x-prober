// import '@babel/polyfill'
// import 'isomorphic-fetch'

import React from 'react'
import { render } from 'react-dom'

// import './style'
// import '~components/Footer/src/components/style'

import ready from '~components/Helper/src/components/ready'
import styled from 'styled-components'
import Title from '~components/Title/src/components'
import Container from '~components/Container/src/components'
import Cards from '~components/Card/src/components'
import Normalize from './style'
import '~components/ServerStatus/src'
import '~components/NetworkStats/src'
import '~components/Ping/src'
import '~components/ServerInfo/src'
import '~components/PhpInfo/src'
import '~components/PhpExtensions/src'
import '~components/Database/src'
import '~components/MyInfo/src'
import Nav from '~components/Nav/src/components'
import store from '../stores'
import Forkme from '~components/Forkme/src/components'

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
    <App ref={c => store.setAppContainer(c)}>
      <Container>
        <Cards />
      </Container>
    </App>
    <Nav />
    <Forkme />
    {/*
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
