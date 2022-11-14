import { FC, useEffect, useState } from 'react'
import { NetworksStatsItem } from '../../NetworkStats/components/item'
import { NetworkStatsItemProps } from '../../NetworkStats/stores'
import styles from './styles.module.scss'
interface NodeNetworksProps {
  items: NetworkStatsItemProps[]
  timestamp: number
}
export const NodeNetworks: FC<NodeNetworksProps> = ({ items, timestamp }) => {
  const itemsCount = items.length
  const [data, setData] = useState({
    curr: { items, timestamp },
    prev: { items, timestamp },
  })
  useEffect(() => {
    setData((prevData) => ({
      curr: {
        items,
        timestamp,
      },
      prev: prevData.curr,
    }))
  }, [items, timestamp])
  if (!itemsCount) {
    return null
  }
  const { curr, prev } = data
  const seconds = curr.timestamp - prev.timestamp
  return (
    <div className={styles.groupNetworks}>
      {items.map(({ id, rx, tx }) => {
        if (!rx && !tx) {
          return null
        }
        const prevItem = prev.items.find((item) => item.id === id)
        const prevRx = prevItem?.rx || 0
        const prevTx = prevItem?.tx || 0
        return (
          <div className={styles.groupNetwork} key={id}>
            <NetworksStatsItem
              id={id}
              singleLine={false}
              totalRx={rx}
              rateRx={(rx - prevRx) / seconds}
              totalTx={tx}
              rateTx={(tx - prevTx) / seconds}
            />
          </div>
        )
      })}
    </div>
  )
}
