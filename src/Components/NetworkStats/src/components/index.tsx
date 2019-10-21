import React, { Component } from 'react'
import { observer } from 'mobx-react'
import formatBytes from '~components/Helper/src/components/format-bytes'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store, { INetworkStatsItem } from '../stores'
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

const StyledData = styled.div`
  flex: 0 0 50%;
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
  private items: INetworkStatsItem = {}

  public render() {
    const { items } = store

    if (!items) {
      return null
    }

    const lastItems = toJS(Object.keys(this.items).length ? this.items : items)

    this.items = items

    return (
      <Row>
        {Object.entries(items).map(([id, { rx, tx }]) => {
          if (!rx && !tx) {
            return null
          }

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
                    <StyledData>
                      <StyledTotal>{formatBytes(rx)}</StyledTotal>
                      <StyledRateRx>
                        {formatBytes(rx - lastItems[id].rx)}/s
                      </StyledRateRx>
                    </StyledData>
                    <StyledData>
                      <StyledTotal>{formatBytes(tx)}</StyledTotal>
                      <StyledRateTx>
                        {formatBytes(tx - lastItems[id].tx)}/s
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
