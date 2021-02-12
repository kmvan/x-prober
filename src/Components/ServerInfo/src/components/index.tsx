import CardGrid from '@/Card/src/components/card-grid'
import Row from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import template from '@/Utils/src/components/template'
import { observer } from 'mobx-react-lite'
import React from 'react'
import store from '../stores'
import ServerDiskUsage from './disk-usage'
const ServerInfo = observer(() => {
  const {
    conf,
    serverUptime: { days, hours, mins, secs },
  } = store
  const uptime = template(
    gettext('{{days}} days {{hours}} hours {{mins}} mins {{secs}} secs'),
    { days, hours, mins, secs }
  )
  const shortItems = [
    [gettext('Server name'), conf?.serverName],
    [gettext('Server time'), store.serverTime],
    [gettext('Server uptime'), uptime],
    [gettext('Server IPv4'), store.serverIpv4],
    [gettext('Server IPv6'), store.serverIpv6],
    [gettext('Server software'), conf?.serverSoftware],
    [gettext('PHP version'), conf?.phpVersion],
  ]
  const longItems = [
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
export default ServerInfo
