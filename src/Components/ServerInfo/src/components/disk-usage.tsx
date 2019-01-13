import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import Portal from '~components/Helper/src/components/portal'
import setProgress from '~components/Helper/src/components/set-progress'

@observer
class ServerInfoDiskUsage extends Component {
  public percentContainer = document.getElementById(
    'inn-diskUsagePercent'
  ) as HTMLElement
  public overviewContainer = document.getElementById(
    'inn-diskUsageOverview'
  ) as HTMLElement
  public progress = document.getElementById(
    'inn-diskUsageProgressValue'
  ) as HTMLElement

  public FetchStore = FetchStore

  public render() {
    const {
      diskUsage: { percent, overview },
    } = this.FetchStore.data as any

    if (!this.percentContainer || !this.overviewContainer || !this.progress) {
      return null
    }

    setProgress(this.progress, percent)

    return (
      <>
        <Portal target={this.percentContainer}>{percent}%</Portal>
        <Portal target={this.overviewContainer}>{overview}</Portal>
      </>
    )
  }
}

export default ServerInfoDiskUsage
