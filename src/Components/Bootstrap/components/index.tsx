import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { createRoot } from 'react-dom/client'
import { Cards } from '../../Card/components'
import '../../ColorScheme/components/config.scss'
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
import './global.scss'
import styles from './styles.module.scss'
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
const Bootstrap: FC = observer(() => (
  <>
    <Title />
    <div className={styles.app}>
      <Container>
        <Cards />
        <Footer />
      </Container>
    </div>
    <Nav />
    <Forkme />
    <Toast />
  </>
))
ready(() => {
  const c = document.createElement('div')
  document.body.innerHTML = ''
  document.body.appendChild(c)
  createRoot(c).render(<Bootstrap />)
})
