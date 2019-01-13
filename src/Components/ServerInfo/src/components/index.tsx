import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import Portal from '~components/Helper/src/components/portal'
import ServerInfoDiskUsage from './disk-usage'

@observer
class ServerInfo extends Component {
  public timeContainer = document.querySelector(
    '.inn-serverInfoTime-group__content'
  ) as HTMLElement
  public upTimeContainer = document.querySelector(
    '.inn-serverInfoUpTime-group__content'
  ) as HTMLElement

  public FetchStore = FetchStore

  public render() {
    if (this.FetchStore.isLoading) {
      return null
    }

    const {
      serverInfo: { time, upTime },
    } = this.FetchStore.data as any

    return (
      <>
        <Portal target={this.timeContainer}>{time}</Portal>
        <Portal target={this.upTimeContainer}>{upTime}</Portal>
        <ServerInfoDiskUsage />
      </>
    )
  }
}

export default ServerInfo
