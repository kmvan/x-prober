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
import { GUTTER, COLOR_DARK, COLOR_GRAY } from '~components/Config/src'

const StyledApp = styled.div`
  padding: calc(${GUTTER} * 3.5) 0 calc(${GUTTER} * 2);
  background: ${COLOR_GRAY};
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);

  /* notch right angle square */
  ::before,
  ::after {
    position: fixed;
    left: 0;
    top: 0;
    right: 0;
    bottom: calc(${GUTTER} * 2);
    border: ${GUTTER} solid ${COLOR_DARK};
    pointer-events: none;
    z-index: 1;
    content: '';
  }
  ::after {
    border-radius: calc(${GUTTER} * 3);
  }
`
const Bootstrap = () => (
  <>
    <Normalize />
    <Title />
    <StyledApp ref={c => store.setAppContainer(c)}>
      <Container>
        <Cards />
        <Footer />
      </Container>
    </StyledApp>
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
