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

const StyledNetworkStatsDataContainer = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
`

interface StyledNetworkStatsDataProps {
  isUpload: boolean
}

const StyledNetworkStatsData = styled.div<StyledNetworkStatsDataProps>`
  flex: 0 0 50%;
  color: ${({ isUpload, theme }) =>
    isUpload ? theme.colorUpload : theme.colorDownload};
`

const StyledNetworkStatsTotal = styled.div``
const StyledNetworkStatsRate = styled.div`
  font-family: 'Arial Black';

  ::before {
    margin-right: 0.5rem;
  }
`

const StyledNetworkStatsRateRx = styled(StyledNetworkStatsRate)`
  ::before {
    content: '▼';
  }
`
const StyledNetworkStatsRateTx = styled(StyledNetworkStatsRate)`
  ::before {
    content: '▲';
  }
`

@observer
export default class NetworkStats extends Component {
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
                  <StyledNetworkStatsDataContainer>
                    <StyledNetworkStatsData isUpload={false}>
                      <StyledNetworkStatsTotal>
                        {formatBytes(rx)}
                      </StyledNetworkStatsTotal>
                      <StyledNetworkStatsRateRx>
                        {formatBytes((rx - lastRx) / seconds)}/s
                      </StyledNetworkStatsRateRx>
                    </StyledNetworkStatsData>
                    <StyledNetworkStatsData isUpload>
                      <StyledNetworkStatsTotal>
                        {formatBytes(tx)}
                      </StyledNetworkStatsTotal>
                      <StyledNetworkStatsRateTx>
                        {formatBytes((tx - lastTx) / seconds)}/s
                      </StyledNetworkStatsRateTx>
                    </StyledNetworkStatsData>
                  </StyledNetworkStatsDataContainer>
                </Grid>
              </StyledNetworkIdRow>
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}
