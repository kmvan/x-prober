import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { GridContainer } from '../../Grid/components/container'
import { gettext } from '../../Language'
import { template } from '../../Utils/components/template'
import { ServerInfoStore } from '../stores'
import { ServerDiskUsage } from './disk-usage'
import { Location } from './location'
const ServerInfoTime: FC = observer(() => {
  const {
    serverUptime: { days, hours, mins, secs },
    serverTime,
  } = ServerInfoStore
  const uptime = template(
    gettext('{{days}} days {{hours}} hours {{mins}} mins {{secs}} secs'),
    { days, hours, mins, secs },
  )
  const items = [
    [gettext('Server time'), serverTime],
    [gettext('Server uptime'), uptime],
  ]
  return (
    <>
      {items.map(([title, content]) => (
        <CardGrid key={title} name={title} lg={2} xl={3} xxl={4}>
          {content}
        </CardGrid>
      ))}
    </>
  )
})
export const ServerInfo: FC = observer(() => {
  const { conf, serverIpv4, serverIpv6 } = ServerInfoStore
  const shortItems1 = [[gettext('Server name'), conf?.serverName]]
  const shortItems2 = [
    [gettext('Server IPv4'), serverIpv4],
    [gettext('Server IPv6'), serverIpv6],
    [gettext('Server software'), conf?.serverSoftware],
  ]
  const longItems = [
    [
      gettext('Server location (IPv4)'),
      <Location key='serverLocalIpv4' action='serverLocationIpv4' />,
    ],
    [gettext('CPU model'), conf?.cpuModel || gettext('Unavailable')],
    [gettext('Server OS'), conf?.serverOs],
    [gettext('Script path'), conf?.scriptPath],
    [gettext('Disk usage'), <ServerDiskUsage key='diskUsage' />],
  ]
  return (
    <GridContainer>
      {shortItems1.map(([title, content]) => (
        <CardGrid key={title} name={title} lg={2} xl={3} xxl={4}>
          {content}
        </CardGrid>
      ))}
      <ServerInfoTime />
      {shortItems2.map(([title, content]) => (
        <CardGrid key={title} name={title} lg={2} xl={3} xxl={4}>
          {content}
        </CardGrid>
      ))}
      {longItems.map(([title, content]) => (
        <CardGrid key={title} name={title}>
          {content}
        </CardGrid>
      ))}
    </GridContainer>
  )
})
