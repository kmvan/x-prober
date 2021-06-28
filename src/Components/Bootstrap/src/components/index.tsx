import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { render } from 'react-dom'
import styled, { ThemeProvider } from 'styled-components'
import { Cards } from '../../../Card/src/components'
import { ColorScheme } from '../../../ColorScheme/src/components'
import { ColorSchemeStore } from '../../../ColorScheme/src/stores'
import { GUTTER } from '../../../Config/src'
import { Container } from '../../../Container/src/components'
import { DatabaseBootstrap } from '../../../Database/src/bootstrap'
import { Footer } from '../../../Footer/src/components'
import { Forkme } from '../../../Forkme/src/components'
import { MyInfoBootstrap } from '../../../MyInfo/src/bootstrap'
import { Nav } from '../../../Nav/src/components'
import { NetworkStatsBoostrap } from '../../../NetworkStats/src/bootstrap'
import { NodesBoostrap } from '../../../Nodes/src/bootstrap'
import { PhpExtensionsBootstrap } from '../../../PhpExtensions/src/bootstrap'
import { PhpInfoBootstrap } from '../../../PhpInfo/src/bootstrap'
import { PingBootstrap } from '../../../Ping/src/bootstrap'
import { ServerBenchmarkBoostrap } from '../../../ServerBenchmark/src/bootstrap'
import { ServerInfoBoostrap } from '../../../ServerInfo/src/bootstrap'
import { ServerStatusBoostrap } from '../../../ServerStatus/src/bootstrap'
import { TemperatureSensorBoostrap } from '../../../TemperatureSensor/src/bootstrap'
import { Title } from '../../../Title/src/components'
import { Toast } from '../../../Toast/src/components'
import { ready } from '../../../Utils/src/components/ready'
import { BootstrapStore } from '../stores'
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
const Bootstrap: FC = observer(() => {
  return (
    <ThemeProvider theme={ColorSchemeStore.scheme}>
      <Normalize />
      <Title />
      <StyledApp ref={(c) => BootstrapStore.setAppContainer(c)}>
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
