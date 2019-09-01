import '@babel/polyfill'
import 'isomorphic-fetch'

import React from 'react'
import { render } from 'react-dom'
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
import '~components/ServerBenchmark/src'
import Nav from '~components/Nav/src/components'
import store from '../stores'
import Forkme from '~components/Forkme/src/components'
import Footer from '~components/Footer/src/components'
import Toast from '~components/Toast/src/components'
import { device } from '~components/Style/src/components/devices'
import { GUTTER, DARK_COLOR } from '~components/Config/src'

const App = styled.div`
  position: fixed;
  top: ${GUTTER};
  left: ${GUTTER};
  right: ${GUTTER};
  bottom: calc(${GUTTER} * 3);
  border-radius: calc(${GUTTER} * 2);
  background: #f8f8f8;
  padding-top: calc(${GUTTER} * 3);
  overflow-y: scroll;
  scroll-behavior: smooth;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
  will-change: transform;

  @media ${device('desktopSm')} {
    ::-webkit-scrollbar-track {
      background-color: transparent;
    }

    ::-webkit-scrollbar {
      width: ${GUTTER};
      background-color: transparent;
    }

    ::-webkit-scrollbar-thumb {
      border-radius: ${GUTTER} 0 0 ${GUTTER};
      background-color: ${DARK_COLOR};
      opacity: 0;

      &:hover {
        opacity: 1;
      }
    }
  }
`
const Bootstrap = () => (
  <>
    <Normalize />
    <Title />
    <App ref={c => store.setAppContainer(c)}>
      <Container>
        <Cards />
        <Footer />
      </Container>
    </App>
    <Nav />
    <Forkme />
    <Toast />
  </>
)

ready(() => {
  const c = document.createElement('div')
  document.body.innerHTML = ''
  document.body.appendChild(c)
  render(<Bootstrap />, c)
})
