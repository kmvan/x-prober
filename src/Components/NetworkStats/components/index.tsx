import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { usePrevious } from 'react-use'
import { CardGrid } from '../../Card/components/card-grid'
import { GridContainer } from '../../Grid/components/container'
import { NetworkStatsStore } from '../stores'
import { NetworksStatsItem } from './item'
export const NetworkStats: FC = observer(() => {
  const { sortItems, itemsCount, timestamp } = NetworkStatsStore
  if (!itemsCount) {
    return null
  }
  const prevData = usePrevious({
    items: sortItems,
    timestamp,
  })
  const seconds = timestamp - (prevData?.timestamp || timestamp)
  return (
    <GridContainer>
      {sortItems.map(({ id, rx, tx }) => {
        if (!rx && !tx) {
          return null
        }
        const prevItem = (prevData?.items || sortItems).find(
          (item) => item.id === id,
        )
        const prevRx = prevItem?.rx || 0
        const prevTx = prevItem?.tx || 0
        return (
          <CardGrid key={id} lg={2} xxl={3}>
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
    </GridContainer>
  )
})
