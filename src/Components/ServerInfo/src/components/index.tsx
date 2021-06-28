import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { CardGrid } from '../../../Card/src/components/card-grid'
import { Row } from '../../../Grid/src/components/row'
import { gettext } from '../../../Language/src'
import { template } from '../../../Utils/src/components/template'
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
    { days, hours, mins, secs }
  )
  const items = [
    [gettext('Server time'), serverTime],
    [gettext('Server uptime'), uptime],
  ]
  return (
    <>
      {items.map(([title, content], i) => (
        <CardGrid
          key={i}
          name={title}
          tablet={[1, 2]}
          desktopMd={[1, 4]}
          desktopLg={[1, 5]}>
          {content}
        </CardGrid>
      ))}
    </>
  )
})
export const ServerInfo: FC = observer(() => {
  const { conf, serverIpv4, serverIpv6 } = ServerInfoStore
  const shortItems1 = [
    [gettext('Server name'), conf?.serverName],
    [gettext('Server IPv4'), serverIpv4],
    [gettext('Server IPv6'), serverIpv6],
    [gettext('Server software'), conf?.serverSoftware],
  ]
  const shortItems2 = [
    [gettext('Server IPv4'), serverIpv4],
    [gettext('Server IPv6'), serverIpv6],
    [gettext('Server software'), conf?.serverSoftware],
  ]
  const longItems = [
    [
      gettext('Server location (IPv4)'),
      <Location action='serverLocationIpv4' />,
    ],
    [gettext('CPU model'), conf?.cpuModel || gettext('Unavailable')],
    [gettext('Server OS'), conf?.serverOs],
    [gettext('Script path'), conf?.scriptPath],
    [gettext('Disk usage'), <ServerDiskUsage />],
  ]
  return (
    <Row>
      {shortItems1.map(([title, content], i) => (
        <CardGrid
          key={i}
          name={title}
          tablet={[1, 2]}
          desktopMd={[1, 4]}
          desktopLg={[1, 5]}>
          {content}
        </CardGrid>
      ))}
      <ServerInfoTime />
      {shortItems2.map(([title, content], i) => (
        <CardGrid
          key={i}
          name={title}
          tablet={[1, 2]}
          desktopMd={[1, 4]}
          desktopLg={[1, 5]}>
          {content}
        </CardGrid>
      ))}
      {longItems.map(([title, content], i) => (
        <CardGrid key={i} name={title} tablet={[1, 1]}>
          {content}
        </CardGrid>
      ))}
    </Row>
  )
})
