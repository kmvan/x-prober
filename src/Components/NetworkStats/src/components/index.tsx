import React, { Component } from 'react'

import { observer } from 'mobx-react'
import FetchStore from '~components/Fetch/src/stores'
import Portal from '~components/Helper/src/components/portal'
import formatBytes from '~components/Helper/src/components/format-bytes'
import './style'

@observer
class NetworkStats extends Component {
  public FetchStore = FetchStore

  public lastRx = {}
  public lastTx = {}

  public render() {
    if (this.FetchStore.isLoading) {
      return null
    }

    const { networkStats } = this.FetchStore.data as any

    if (!networkStats) {
      return null
    }

    return (
      <>
        {Object.keys(networkStats).map((ethName: string) => {
          const content: any[] = []
          const { rx, tx } = networkStats[ethName]

          if (!rx && !tx) {
            return null
          }

          ethName = encodeURIComponent(ethName)

          const rxContainer = document.getElementById(
            `inn-network-stats__rx__total__${ethName}`
          ) as HTMLElement
          const txRateContainer = document.getElementById(
            `inn-network-stats__tx__rate__${ethName}`
          ) as HTMLElement

          const txContainer = document.getElementById(
            `inn-network-stats__tx__total__${ethName}`
          ) as HTMLElement

          const rxRateContainer = document.getElementById(
            `inn-network-stats__rx__rate__${ethName}`
          ) as HTMLElement

          // total rx
          if (rxContainer) {
            content.push(
              <Portal key="rx" target={rxContainer}>
                {formatBytes(rx)}
              </Portal>
            )
          }

          // rate rx
          if (rxRateContainer) {
            if (!this.lastRx[ethName]) {
              this.lastRx[ethName] = rx
            }

            content.push(
              <Portal key="rxRate" target={rxRateContainer}>
                {formatBytes(rx - this.lastRx[ethName])}
              </Portal>
            )

            if (this.lastRx[ethName] !== rx) {
              this.lastRx[ethName] = rx
            }
          }

          // total tx
          if (txContainer) {
            content.push(
              <Portal key="tx" target={txContainer}>
                {formatBytes(tx)}
              </Portal>
            )
          }

          // rate tx
          if (txRateContainer) {
            if (!this.lastTx[ethName]) {
              this.lastTx[ethName] = tx
            }

            content.push(
              <Portal key="txRate" target={txRateContainer}>
                {formatBytes(tx - this.lastTx[ethName])}
              </Portal>
            )

            if (this.lastTx[ethName] !== tx) {
              this.lastTx[ethName] = tx
            }
          }

          return content
        })}
      </>
    )
  }
}

export default NetworkStats
