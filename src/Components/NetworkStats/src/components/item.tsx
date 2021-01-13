import formatBytes from '@/Helper/src/components/format-bytes'
import Grid from '@/Grid/src/components/grid'
import React from 'react'
import Row from '@/Grid/src/components/row'
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
export default NetworksStatsItem
