import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import Portal from '~components/Helper/src/components/portal'

@observer
class SystemLoad extends Component {
  public FetchStore = FetchStore

  public container = document.querySelector(
    '.inn-systemLoadAvg-group__content'
  ) as HTMLElement

  public render() {
    const { sysLoadAvg } = this.FetchStore.data as any

    return (
      <>
        <Portal target={this.container}>
          {sysLoadAvg.map((avg: number, i: number) => {
            return (
              <div key={i} className="inn-system-load-avg__group">
                {avg.toFixed(2)}
              </div>
            )
          })}
        </Portal>
      </>
    )
  }
}

export default SystemLoad
