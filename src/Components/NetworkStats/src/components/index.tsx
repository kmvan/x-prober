import React, { Component } from 'react'
import { observer } from 'mobx-react'
import formatBytes from '~components/Helper/src/components/format-bytes'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store, { INetworkStatsItem } from '../stores'
import Grid from '~components/Grid/src/components/grid'
import styled from 'styled-components'
import { toJS } from 'mobx'

const NetworkId = styled.div`
  text-decoration: underline;
`

const NetworkIdRow = styled(Row)`
  align-items: center;
  justify-content: center;
  text-align: center;
`

const DataContainer = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
`

const Data = styled.div`
  flex: 0 0 50%;
`

const Total = styled.div``
const Rate = styled.div`
  font-family: 'Arial Black';

  ::before {
    margin-right: 0.5rem;
  }
`

const RateRx = styled(Rate)`
  ::before {
    content: '▼';
  }
`
const RateTx = styled(Rate)`
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
            <CardGrid key={id} tablet={[1, 2]}>
              <NetworkIdRow>
                <Grid mobileSm={[1, 3]}>
                  <NetworkId>{id}</NetworkId>
                </Grid>
                <Grid mobileSm={[2, 3]}>
                  <DataContainer>
                    <Data>
                      <Total>{formatBytes(rx)}</Total>
                      <RateRx>{formatBytes(rx - lastItems[id].rx)}</RateRx>
                    </Data>
                    <Data>
                      <Total>{formatBytes(tx)}</Total>
                      <RateTx>{formatBytes(tx - lastItems[id].tx)}</RateTx>
                    </Data>
                  </DataContainer>
                </Grid>
              </NetworkIdRow>
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default NetworkStats
