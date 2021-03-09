import { CardGrid } from '@/Card/src/components/card-grid'
import { Row } from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import { template } from '@/Utils/src/components/template'
import { observer } from 'mobx-react-lite'
import React from 'react'
import { ServerInfoStore } from '../stores'
import { ServerDiskUsage } from './disk-usage'
import { Location } from './location'
export const ServerInfo = observer(() => {
  const {
    conf,
    serverUptime: { days, hours, mins, secs },
    serverTime,
    serverIpv4,
    serverIpv6,
  } = ServerInfoStore
  const uptime = template(
    gettext('{{days}} days {{hours}} hours {{mins}} mins {{secs}} secs'),
    { days, hours, mins, secs }
  )
  const shortItems = [
    [gettext('Server name'), conf?.serverName],
    [gettext('Server time'), serverTime],
    [gettext('Server uptime'), uptime],
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
      {shortItems.map(([title, content], i) => {
        return (
          <CardGrid
            key={i}
            name={title}
            tablet={[1, 2]}
            desktopMd={[1, 4]}
            desktopLg={[1, 5]}>
            {content}
          </CardGrid>
        )
      })}
      {longItems.map(([title, content], i) => {
        return (
          <CardGrid key={i} name={title} tablet={[1, 1]}>
            {content}
          </CardGrid>
        )
      })}
    </Row>
  )
})
