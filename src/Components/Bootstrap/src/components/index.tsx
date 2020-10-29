import React from 'react'
import { render } from 'react-dom'
import ready from '@/Helper/src/components/ready'
import styled, { ThemeProvider } from 'styled-components'
import Title from '@/Title/src/components'
import Container from '@/Container/src/components'
import Cards from '@/Card/src/components'
import Normalize from './style'
import '@/Nodes/src'
import '@/ServerStatus/src'
import '@/NetworkStats/src'
import '@/TemperatureSensor/src'
import '@/Ping/src'
import '@/ServerInfo/src'
import '@/PhpInfo/src'
import '@/PhpExtensions/src'
import '@/Database/src'
import '@/MyInfo/src'
import '@/ServerBenchmark/src'
import Nav from '@/Nav/src/components'
import store from '../stores'
import Forkme from '@/Forkme/src/components'
import Footer from '@/Footer/src/components'
import Toast from '@/Toast/src/components'
import ColorScheme from '@/ColorScheme/src/components'
import { GUTTER } from '@/Config/src'
import { rgba } from 'polished'
import ColorSchemeStore from '@/ColorScheme/src/stores'
import { observer } from 'mobx-react-lite'

const StyledApp = styled.div`
  padding: calc(${GUTTER} * 3.5) 0 calc(${GUTTER} * 2);
  background: ${({ theme }) => theme.colorGray};
  box-shadow: inset 0 0 5px ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};

  /* notch right angle square */
  ::before,
  ::after {
    position: fixed;
    left: 0;
    top: 0;
    right: 0;
    bottom: calc(${GUTTER} * 2);
    border: ${GUTTER} solid ${({ theme }) => theme.colorDark};
    pointer-events: none;
    z-index: 1;
    content: '';
  }
  ::after {
    border-radius: calc(${GUTTER} * 3);
  }
`

const Bootstrap = observer(() => {
  return (
    <ThemeProvider theme={ColorSchemeStore.scheme}>
      <Normalize />
      <Title />
      <StyledApp ref={c => store.setAppContainer(c)}>
        <Container>
          <ColorScheme />
          <Cards />
          <Footer />
        </Container>
      </StyledApp>
      <Nav />
      <Forkme />
      <Toast />
    </ThemeProvider>
  )
})

ready(() => {
  const c = document.createElement('div')
  document.body.innerHTML = ''
  document.body.appendChild(c)
  render(<Bootstrap />, c)
})
