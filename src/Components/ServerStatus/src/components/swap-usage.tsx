import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import setProgress from '~components/Helper/src/components/set-progress'
import Portal from '~components/Helper/src/components/portal'

@observer
class SwapUsage extends Component {
  public FetchStore = FetchStore

  public percentContainer = document.getElementById(
    'inn-swapRealUsagePercent'
  ) as HTMLElement
  public overviewContainer = document.getElementById(
    'inn-swapRealUsage'
  ) as HTMLElement
  public progress = document.getElementById(
    'inn-swapRealUsageProgressValue'
  ) as HTMLElement

  public render() {
    if (!this.percentContainer || !this.overviewContainer) {
      return null
    }

    const {
      swapRealUsage: { percent, overview },
    } = this.FetchStore.data as any

    if (!overview) {
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

export default SwapUsage
