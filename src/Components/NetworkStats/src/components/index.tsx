import React, { Component } from 'react'
import { observer } from 'mobx-react'
import formatBytes from '~components/Helper/src/components/format-bytes'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store, { NetworkStatsItemProps } from '../stores'
import Grid from '~components/Grid/src/components/grid'
import styled from 'styled-components'
import { toJS } from 'mobx'

const StyledNetworkId = styled.div`
  text-decoration: underline;
`

const StyledNetworkIdRow = styled(Row)`
  align-items: center;
  justify-content: center;
  text-align: center;
`

const StyledDataContainer = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
`

interface StyledDataProps {
  isUpload: boolean
}

const StyledData = styled.div<StyledDataProps>`
  flex: 0 0 50%;
  color: ${({ isUpload, theme }) =>
    isUpload ? theme.colorUpload : theme.colorDownload};
`

const StyledTotal = styled.div``
const StyledRate = styled.div`
  font-family: 'Arial Black';

  ::before {
    margin-right: 0.5rem;
  }
`

const StyledRateRx = styled(StyledRate)`
  ::before {
    content: '▼';
  }
`
const StyledRateTx = styled(StyledRate)`
  ::before {
    content: '▲';
  }
`

@observer
class NetworkStats extends Component {
  private items: NetworkStatsItemProps = {}
  private timestamp: number = 0

  public render() {
    const { items, timestamp } = store

    if (!items) {
      return null
    }

    let seconds = timestamp - this.timestamp
    seconds = seconds < 1 ? 1 : seconds
    const lastTimestamp = timestamp
    const lastItems = toJS(Object.keys(this.items).length ? this.items : items)

    this.items = items
    this.timestamp = timestamp

    return (
      <Row>
        {Object.entries(items).map(([id, { rx, tx }]) => {
          if (!rx && !tx) {
            return null
          }

          const lastRx = lastItems?.[id]?.rx || 0
          const lastTx = lastItems?.[id]?.tx || 0

          return (
            <CardGrid
              key={id}
              tablet={[1, 2]}
              desktopMd={[1, 3]}
              desktopLg={[1, 4]}
            >
              <StyledNetworkIdRow>
                <Grid mobileSm={[1, 3]}>
                  <StyledNetworkId>{id}</StyledNetworkId>
                </Grid>
                <Grid mobileSm={[2, 3]}>
                  <StyledDataContainer>
                    <StyledData isUpload={false}>
                      <StyledTotal>{formatBytes(rx)}</StyledTotal>
                      <StyledRateRx>
                        {formatBytes((rx - lastRx) / seconds)}/s
                      </StyledRateRx>
                    </StyledData>
                    <StyledData isUpload>
                      <StyledTotal>{formatBytes(tx)}</StyledTotal>
                      <StyledRateTx>
                        {formatBytes((tx - lastTx) / seconds)}/s
                      </StyledRateTx>
                    </StyledData>
                  </StyledDataContainer>
                </Grid>
              </StyledNetworkIdRow>
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default NetworkStats
