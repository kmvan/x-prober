import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import setProgress from '~components/Helper/src/components/set-progress'
import Portal from '~components/Helper/src/components/portal'

@observer
class CpuUsage extends Component {
  public FetchStore = FetchStore

  public container = document.getElementById(
    'inn-cpuUsagePercent'
  ) as HTMLElement
  public progress = document.getElementById(
    'inn-cpuUsageProgressValue'
  ) as HTMLElement

  public render() {
    const { cpuUsage } = this.FetchStore.data as any
    const usage = 100 - ~~cpuUsage.idle

    setProgress(this.progress, usage)

    return (
      <>
        <Portal target={this.container}>{usage}%</Portal>
      </>
    )
  }
}

export default CpuUsage
