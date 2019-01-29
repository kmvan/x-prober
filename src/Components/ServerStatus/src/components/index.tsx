import React, { Component } from 'react'

import './style'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import SystemLoad from './system-load'
import CpuUsage from './cpu-usage'
import SwapUsage from './swap-usage'
import MemUsage from './mem-usage'
import MemCached from './mem-cached'
import MemBuffers from './mem-buffers'
import SwapCached from './swap-cached'

@observer
class ServerStatus extends Component {
  public FetchStore = FetchStore

  public render() {
    if (this.FetchStore.isLoading) {
      return null
    }

    return (
      <>
        <SystemLoad />
        <CpuUsage />
        <MemUsage />
        <MemCached />
        <MemBuffers />
        <SwapUsage />
        <SwapCached />
      </>
    )
  }
}

export default ServerStatus
