import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import setProgress from '~components/Helper/src/components/set-progress'
import Portal from '~components/Helper/src/components/portal'

@observer
class MemoryUsage extends Component {
  public FetchStore = FetchStore

  public percentContainer = document.getElementById(
    'inn-memRealUsagePercent'
  ) as HTMLElement
  public overviewContainer = document.getElementById(
    'inn-memRealUsageOverview'
  ) as HTMLElement
  public progress = document.getElementById(
    'inn-memRealUsageProgressValue'
  ) as HTMLElement

  public render() {
    const {
      memRealUsage: { percent, overview },
    } = this.FetchStore.data as any

    setProgress(this.progress, percent)

    return (
      <>
        <Portal target={this.percentContainer}>{percent}%</Portal>
        <Portal target={this.overviewContainer}>{overview}</Portal>
      </>
    )
  }
}

export default MemoryUsage
