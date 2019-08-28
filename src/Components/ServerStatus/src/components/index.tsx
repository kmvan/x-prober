import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Row from '~components/Grid/src/components/row'
import { gettext } from '~components/Language/src'
import store from '../stores'
import SystemLoad from './system-load'
import CpuUsage from './cpu-usage'
import MemRealUsage from './mem-real-usage'
import MemCached from './mem-cached'
import SwapUsage from './swap-usage'
import SwapCached from './swap-cached'

@observer
class ServerStatus extends Component {
  public render() {
    return (
      <Row>
        <SystemLoad />
        <CpuUsage />
        <MemRealUsage />
        <MemCached />
        <SwapUsage />
        <SwapCached />
      </Row>
    )
  }
}

export default ServerStatus
