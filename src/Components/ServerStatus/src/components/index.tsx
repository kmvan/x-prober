import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import SystemLoad from './system-load'
import CpuUsage from './cpu-usage'
import SwapUsage from './swap-usage'
import MemoryUsage from './memory-usage'

import './style'

@observer
class ServerStatus extends Component {
  public FetchStore = FetchStore

  public systemLoadAvgContainer = document.getElementById(
    'inn-systemLoadAvg'
  ) as HTMLElement

  public render() {
    if (this.FetchStore.isLoading) {
      return null
    }

    return (
      <>
        <SystemLoad />
        <CpuUsage />
        <MemoryUsage />
        <SwapUsage />
      </>
    )
  }
}

export default ServerStatus
