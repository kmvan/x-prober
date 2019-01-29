import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import setProgress from '~components/Helper/src/components/set-progress'
import Portal from '~components/Helper/src/components/portal'
import formatBytes from '~components/Helper/src/components/format-bytes'

@observer
class SwapCached extends Component {
  public FetchStore = FetchStore

  public percentContainer = document.getElementById(
    'inn-swapCachedPercent'
  ) as HTMLElement
  public overviewContainer = document.getElementById(
    'inn-swapCachedOverview'
  ) as HTMLElement
  public progress = document.getElementById(
    'inn-swapCachedProgressValue'
  ) as HTMLElement

  public render() {
    if (!this.percentContainer || !this.overviewContainer) {
      return null
    }

    const {
      swapCached: { total, usage },
    } = this.FetchStore.data as any

    if (!total) {
      return null
    }

    const percent = Math.floor((usage / total) * 1000) / 10
    const overview = `${formatBytes(usage)} / ${formatBytes(total)}`

    setProgress(this.progress, percent)

    return (
      <>
        <Portal target={this.percentContainer}>{percent}%</Portal>
        <Portal target={this.overviewContainer}>{overview}</Portal>
      </>
    )
  }
}

export default SwapCached
