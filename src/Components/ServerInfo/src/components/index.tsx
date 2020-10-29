import React from 'react'
import Row from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import store from '../stores'
import CardGrid from '@/Card/src/components/card-grid'
import ProgressBar from '@/ProgressBar/src/components'
import template from '@/Helper/src/components/template'
import FetchStore from '@/Fetch/src/stores'
import { observer } from 'mobx-react-lite'

const DiskUsage = observer(() => {
  const { ID } = store
  const { isLoading, data } = FetchStore

  let {
    conf: {
      diskUsage: { value, max },
    },
  } = store

  if (!isLoading) {
    value = data?.[ID]?.diskUsage?.value
    max = data?.[ID]?.diskUsage?.max
  }

  if (!value || !max) {
    return <>{gettext('Unavailable')}</>
  }

  return <ProgressBar value={value} max={max} isCapacity />
})

const ServerInfo = observer(() => {
  const {
    conf,
    serverUptime: { days, hours, mins, secs },
  } = store
  const uptime = template(
    gettext('${days} days ${hours} hours ${mins} mins ${secs} secs'),
    { days, hours, mins, secs }
  )
  const shortItems = [
    [gettext('Server name'), conf?.serverName],
    [gettext('Server time'), store.serverTime],
    [gettext('Server uptime'), uptime],
    [gettext('Server IP'), conf?.serverIp],
    [gettext('Server software'), conf?.serverSoftware],
    [gettext('PHP version'), conf?.phpVersion],
  ]

  const longItems = [
    [gettext('CPU model'), conf?.cpuModel || gettext('Unavailable')],
    [gettext('Server OS'), conf?.serverOs],
    [gettext('Script path'), conf?.scriptPath],
    [gettext('Disk usage'), <DiskUsage />],
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
            desktopLg={[1, 5]}
          >
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
