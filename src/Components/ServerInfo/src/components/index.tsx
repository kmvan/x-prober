import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Row from '~components/Grid/src/components/row'
import { gettext } from '~components/Language/src'
import store from '../stores'
import CardGrid from '~components/Card/src/components/card-grid'
import ProgressBar from '~components/ProgressBar/src/components'
import { template, get } from 'lodash-es'
import FetchStore from '~components/Fetch/src/stores'

@observer
class ServerInfo extends Component {
  private diskUsage() {
    const { ID } = store
    const { isLoading, data } = FetchStore

    let {
      conf: {
        diskUsage: { value, max },
      },
    } = store

    if (!isLoading) {
      value = get(data, `${ID}.diskUsage.value`)
      max = get(data, `${ID}.diskUsage.max`)
    }

    if (!value || !max) {
      return gettext('Unavailable')
    }

    return <ProgressBar value={value} max={max} isCapacity={true} />
  }

  public render() {
    const {
      conf,
      serverUptime: { days, hours, mins, secs },
    } = store
    const uptime = template(
      gettext(
        '<%= days %> days <%= hours %> hours <%= mins %> mins <%= secs %> secs'
      )
    )({ days, hours, mins, secs })
    const shortItems = [
      [gettext('Server name'), conf.serverName],
      [gettext('Server time'), store.serverTime],
      [gettext('Server uptime'), uptime],
      [gettext('Server IP'), conf.serverIp],
      [gettext('Server software'), conf.serverSoftware],
      [gettext('PHP version'), conf.phpVersion],
    ]

    const longItems = [
      [gettext('CPU model'), conf.cpuModel || gettext('Unavailable')],
      [gettext('Server OS'), conf.serverOs],
      [gettext('Script path'), conf.scriptPath],
      [gettext('Disk usage'), this.diskUsage()],
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
  }
}

export default ServerInfo
