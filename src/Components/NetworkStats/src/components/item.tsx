import { Grid } from '@/Grid/src/components/grid'
import { Row } from '@/Grid/src/components/row'
import { formatBytes } from '@/Utils/src/components/format-bytes'
import React from 'react'
import styled from 'styled-components'
interface NetworksStatsItemProps {
  id: string
  singleLine?: boolean
  totalRx: number
  rateRx: number
  totalTx: number
  rateTx: number
}
const StyledNetworkId = styled.div`
  text-decoration: underline;
`
export const StyledNetworkIdRow = styled(Row)`
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
    isUpload ? theme['network.stats.upload'] : theme['network.stats.download']};
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
export const NetworksStatsItem = ({
  id,
  singleLine = true,
  totalRx = 0,
  rateRx = 0,
  totalTx = 0,
  rateTx = 0,
}: NetworksStatsItemProps) => {
  if (!id) {
    return null
  }
  return (
    <StyledNetworkIdRow>
      <Grid mobileSm={singleLine ? [1, 3] : [1, 1]}>
        <StyledNetworkId>{id}</StyledNetworkId>
      </Grid>
      <Grid mobileSm={singleLine ? [2, 3] : [1, 1]}>
        <StyledNetworkStatsDataContainer>
          <StyledNetworkStatsData isUpload={false}>
            <StyledNetworkStatsTotal>
              {formatBytes(totalRx)}
            </StyledNetworkStatsTotal>
            <StyledNetworkStatsRateRx>
              {formatBytes(rateRx)}/s
            </StyledNetworkStatsRateRx>
          </StyledNetworkStatsData>
          <StyledNetworkStatsData isUpload>
            <StyledNetworkStatsTotal>
              {formatBytes(totalTx)}
            </StyledNetworkStatsTotal>
            <StyledNetworkStatsRateTx>
              {formatBytes(rateTx)}/s
            </StyledNetworkStatsRateTx>
          </StyledNetworkStatsData>
        </StyledNetworkStatsDataContainer>
      </Grid>
    </StyledNetworkIdRow>
  )
}
