import { Cards } from '@/Components/Card/components'
import '@/Components/ColorScheme/components/config.scss'
import { Container } from '@/Components/Container/components'
import { DatabaseBootstrap } from '@/Components/Database/bootstrap'
import { DiskUsageBootstrap } from '@/Components/DiskUsage/bootstrap'
import { Footer } from '@/Components/Footer/components'
import { Forkme } from '@/Components/Forkme/components'
import { MyInfoBootstrap } from '@/Components/MyInfo/bootstrap'
import { Nav } from '@/Components/Nav/components'
import { NetworkStatsBoostrap } from '@/Components/NetworkStats/bootstrap'
import { NodesBoostrap } from '@/Components/Nodes/bootstrap'
import { PhpExtensionsBootstrap } from '@/Components/PhpExtensions/bootstrap'
import { PhpInfoBootstrap } from '@/Components/PhpInfo/bootstrap'
import { PingBootstrap } from '@/Components/Ping/bootstrap'
import { ServerBenchmarkBoostrap } from '@/Components/ServerBenchmark/bootstrap'
import { ServerInfoBoostrap } from '@/Components/ServerInfo/bootstrap'
import { ServerStatusBoostrap } from '@/Components/ServerStatus/bootstrap'
import { TemperatureSensorBoostrap } from '@/Components/TemperatureSensor/bootstrap'
import { Title } from '@/Components/Title/components'
import { Toast } from '@/Components/Toast/components'
import { ready } from '@/Components/Utils/components/ready'
import { FC } from 'react'
import { createRoot } from 'react-dom/client'
import './global.scss'
import styles from './styles.module.scss'
DatabaseBootstrap()
MyInfoBootstrap()
DiskUsageBootstrap()
NetworkStatsBoostrap()
NodesBoostrap()
PhpExtensionsBootstrap()
PhpInfoBootstrap()
PingBootstrap()
ServerBenchmarkBoostrap()
ServerInfoBoostrap()
ServerStatusBoostrap()
TemperatureSensorBoostrap()
const Bootstrap: FC = () => (
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
)
ready(() => {
  const c = document.createElement('div')
  document.body.innerHTML = ''
  document.body.appendChild(c)
  createRoot(c).render(<Bootstrap />)
})
