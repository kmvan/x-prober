import React, { ReactNode } from 'react'
import Grid, { IGrid } from '~components/Grid/src/components/grid'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'
import { GUTTER } from '~components/Config/src'

export interface ICardGrid extends IGrid {
  title?: ReactNode
  children: ReactNode
}

const StyledCardGroup = styled.div`
  display: flex;
  width: 100%;
  align-items: center;
  border-bottom: 1px solid #eee;
  :hover {
    background: linear-gradient(90deg, #0000, #0000000d, #0000);
  }
`

const StyledCardTitle = styled.div`
  word-break: normal;
  padding: calc(${GUTTER} / 2) ${GUTTER};
  flex: 0 0 8rem;

  @media ${device('tablet')} {
    flex: 0 0 12rem;
  }
`

const StyledCardContent = styled.div`
  flex-grow: 1;
  padding: calc(${GUTTER} / 2) ${GUTTER} calc(${GUTTER} / 2) 0;
`

const CardGrid = ({ title = '', children, ...props }: ICardGrid) => {
  return (
    <Grid {...props}>
      <StyledCardGroup>
        {title && <StyledCardTitle>{title}</StyledCardTitle>}
        <StyledCardContent>{children}</StyledCardContent>
      </StyledCardGroup>
    </Grid>
  )
}

export default CardGrid
