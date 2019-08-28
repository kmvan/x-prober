import React, { Component } from 'react'
import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import Row from '~components/Grid/src/components/row'
import { gettext } from '~components/Language/src'
import store from '../stores'
import CardGrid from '~components/Card/src/components/card-grid'
import ProgressBar from '~components/ProgressBar/src/components'

@observer
class ServerInfo extends Component {
  private diskUsage() {
    const {
      conf: {
        diskUsage: { value, max },
      },
    } = store

    if (!value || !max) {
      return gettext('Unavailable')
    }

    return <ProgressBar value={value} max={max} isCapacity={true} />
  }

  public render() {
    const { conf } = store
    const shortItems = [
      [gettext('Server name'), conf.serverName],
      [gettext('Server time'), store.serverTime],
      [gettext('Server uptime'), store.serverUptime],
      [gettext('Server IP'), conf.serverIp],
      [gettext('Server software'), conf.serverSoftware],
      [gettext('PHP version'), conf.phpVersion],
    ]

    const longItems = [
      [gettext('CPU model'), conf.cpuModel],
      [gettext('Server OS'), conf.serverOs],
      [gettext('Script path'), conf.scriptPath],
      [gettext('Disk usage'), this.diskUsage()],
    ]

    return (
      <Row>
        {shortItems.map(([title, content]) => {
          return (
            <CardGrid key={title} title={title} tablet={[1, 3]}>
              {content}
            </CardGrid>
          )
        })}
        {longItems.map(([title, content]) => {
          return (
            <CardGrid key={title} title={title} tablet={[1, 1]}>
              {content}
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default ServerInfo
