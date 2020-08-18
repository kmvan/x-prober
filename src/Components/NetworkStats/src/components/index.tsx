import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store, { NetworkStatsItemProps } from '../stores'
import { toJS } from 'mobx'
import NetworksStatsItem from './item'

@observer
export default class NetworkStats extends Component {
  private prevItems: NetworkStatsItemProps[] = []
  private prevTimestamp: number = 0

  public render() {
    const { sortItems, itemsCount, timestamp } = store

    if (!itemsCount) {
      return null
    }

    let seconds = timestamp - this.prevTimestamp
    seconds = seconds < 1 ? 1 : seconds
    const prevItems = toJS(itemsCount ? this.prevItems : sortItems)

    this.prevItems = toJS(sortItems)
    this.prevTimestamp = timestamp

    return (
      <Row>
        {sortItems.map(({ id, rx, tx }) => {
          if (!rx && !tx) {
            return null
          }
          const prevItem = prevItems.find(item => item.id === id)
          const prevRx = prevItem?.rx || 0
          const prevTx = prevItem?.tx || 0

          return (
            <CardGrid
              key={id}
              tablet={[1, 2]}
              desktopMd={[1, 3]}
              desktopLg={[1, 4]}
            >
              <NetworksStatsItem
                id={id}
                totalRx={rx}
                rateRx={(rx - prevRx) / seconds}
                totalTx={tx}
                rateTx={(tx - prevTx) / seconds}
              />
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}
