import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { createRoot } from 'react-dom/client'
import styled, { ThemeProvider } from 'styled-components'
import { Cards } from '../../Card/components'
import { ColorScheme } from '../../ColorScheme/components'
import { ColorSchemeStore } from '../../ColorScheme/stores'
import { GUTTER } from '../../Config'
import { Container } from '../../Container/components'
import { DatabaseBootstrap } from '../../Database/bootstrap'
import { Footer } from '../../Footer/components'
import { Forkme } from '../../Forkme/components'
import { MyInfoBootstrap } from '../../MyInfo/bootstrap'
import { Nav } from '../../Nav/components'
import { NetworkStatsBoostrap } from '../../NetworkStats/bootstrap'
import { NodesBoostrap } from '../../Nodes/bootstrap'
import { PhpExtensionsBootstrap } from '../../PhpExtensions/bootstrap'
import { PhpInfoBootstrap } from '../../PhpInfo/bootstrap'
import { PingBootstrap } from '../../Ping/bootstrap'
import { ServerBenchmarkBoostrap } from '../../ServerBenchmark/bootstrap'
import { ServerInfoBoostrap } from '../../ServerInfo/bootstrap'
import { ServerStatusBoostrap } from '../../ServerStatus/bootstrap'
import { TemperatureSensorBoostrap } from '../../TemperatureSensor/bootstrap'
import { Title } from '../../Title/components'
import { Toast } from '../../Toast/components'
import { ready } from '../../Utils/components/ready'
import { Normalize } from './style'
DatabaseBootstrap()
MyInfoBootstrap()
NetworkStatsBoostrap()
NodesBoostrap()
PhpExtensionsBootstrap()
PhpInfoBootstrap()
PingBootstrap()
ServerBenchmarkBoostrap()
ServerInfoBoostrap()
ServerStatusBoostrap()
TemperatureSensorBoostrap()
const StyledApp = styled.div`
  padding: calc(${GUTTER} * 3.5) 0 calc(${GUTTER} * 2);
  background: ${({ theme }) => theme['app.bg']};
  /* notch right angle square */
  ::before,
  ::after {
    position: fixed;
    left: 0;
    top: 0;
    right: 0;
    bottom: calc(${GUTTER} * 2);
    border: ${GUTTER} solid ${({ theme }) => theme['app.border']};
    pointer-events: none;
    z-index: 1;
    content: '';
  }
  ::after {
    border-radius: calc(${GUTTER} * 3);
  }
`
const Bootstrap: FC = observer(() => (
  <ThemeProvider theme={ColorSchemeStore.scheme}>
    <Normalize />
    <Title />
    <StyledApp>
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
))
ready(() => {
  const c = document.createElement('div')
  document.body.innerHTML = ''
  document.body.appendChild(c)
  createRoot(c).render(<Bootstrap />)
})
