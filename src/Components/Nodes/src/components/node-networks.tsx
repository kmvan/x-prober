import React, { useEffect, useState } from 'react'
import styled from 'styled-components'
import { GUTTER, BORDER_RADIUS } from '@/Config/src'
import NetworksStatsItem from '@/NetworkStats/src/components/item'
import { NetworkStatsItemProps } from '@/NetworkStats/src/stores'
import { rgba } from 'polished'

const StyledNodeGroupNetworks = styled.div`
  border-radius: ${BORDER_RADIUS};
  background: ${({ theme }) => rgba(theme.colorDark, 0.1)};
  color: ${({ theme }) => theme.colorDark};
  padding: ${GUTTER};
  margin-bottom: ${GUTTER};
`
const StyledNodeGroupNetwork = styled.div`
  border-bottom: 1px dashed ${({ theme }) => rgba(theme.colorDark, 0.1)};
  margin-bottom: calc(${GUTTER} / 2);
  padding-bottom: calc(${GUTTER} / 2);
  &:last-child {
    margin-bottom: 0;
    border-bottom: 0;
    padding-bottom: 0;
  }
`

interface NodeNetworksProps {
  items: NetworkStatsItemProps[]
  timestamp: number
}

const NodeNetworks = ({ items, timestamp }: NodeNetworksProps) => {
  const itemsCount = items.length

  if (!itemsCount) {
    return null
  }

  const [data, setData] = useState({
    curr: { items, timestamp },
    prev: { items, timestamp },
  })

  useEffect(() => {
    setData(prevData => {
      return {
        curr: {
          items,
          timestamp,
        },
        prev: prevData.curr,
      }
    })
  }, [timestamp])

  const { curr, prev } = data
  const seconds = curr.timestamp - prev.timestamp

  return (
    <StyledNodeGroupNetworks>
      {items.map(({ id, rx, tx }) => {
        if (!rx && !tx) {
          return null
        }

        const prevItem = prev.items.find(item => item.id === id)
        const prevRx = prevItem?.rx || 0
        const prevTx = prevItem?.tx || 0

        return (
          <StyledNodeGroupNetwork key={id}>
            <NetworksStatsItem
              id={id}
              singleLine={false}
              totalRx={rx}
              rateRx={(rx - prevRx) / seconds}
              totalTx={tx}
              rateTx={(tx - prevTx) / seconds}
            />
          </StyledNodeGroupNetwork>
        )
      })}
    </StyledNodeGroupNetworks>
  )
}

export default NodeNetworks
