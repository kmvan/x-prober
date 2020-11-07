import React from 'react'
import Row from '@/Grid/src/components/row'
import CardGrid from '@/Card/src/components/card-grid'
import store from '../stores'
import NetworksStatsItem from './item'
import { observer } from 'mobx-react-lite'
import { usePrevious } from 'react-use'

const NetworkStats = observer(() => {
  const { sortItems, itemsCount, timestamp } = store

  if (!itemsCount) {
    return null
  }

  const prevData = usePrevious({
    items: sortItems,
    timestamp,
  })
  const seconds = timestamp - (prevData?.timestamp || timestamp)

  return (
    <Row>
      {sortItems.map(({ id, rx, tx }) => {
        if (!rx && !tx) {
          return null
        }

        const prevItem = (prevData?.items || sortItems).find(
          item => item.id === id
        )
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
})

export default NetworkStats
