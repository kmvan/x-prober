import React, { Component } from 'react'
import { observer } from 'mobx-react'
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

@observer
export default class NodeNetworks extends Component<NodeNetworksProps> {
  private prevItems: NetworkStatsItemProps[] = []
  private prevTimestamp: number = 0

  public render() {
    const { items, timestamp } = this.props
    const itemsCount = items.length

    if (!itemsCount) {
      return null
    }

    let seconds = timestamp - this.prevTimestamp
    seconds = seconds < 1 ? 1 : seconds
    const prevItems = itemsCount ? this.prevItems : items

    this.prevItems = items
    this.prevTimestamp = timestamp

    return (
      <StyledNodeGroupNetworks>
        {items.map(({ id, rx, tx }) => {
          if (!rx && !tx) {
            return null
          }
          const prevItem = prevItems.find(item => item.id === id)
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
}
