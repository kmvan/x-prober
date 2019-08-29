import React, { ReactNode } from 'react'
import Grid, { IGrid } from '~components/Grid/src/components/grid'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'

export interface ICardGrid extends IGrid {
  title?: ReactNode
  children: ReactNode
}

const CardGroup = styled.div`
  display: flex;
  width: 100%;
  align-items: center;
  border-bottom: 1px solid #eee;
  :hover {
    background: linear-gradient(90deg, #0000, #0000000d, #0000);
  }
`
const CardTitle = styled.div`
  word-break: normal;
  padding: 0.5rem 1rem;
  flex: 0 0 8rem;
  @media ${device('tablet')} {
    flex: 0 0 12rem;
  }
`
const CardContent = styled.div`
  flex-grow: 1;
  padding: 0.5rem 1rem;
`

const CardGrid = ({ title = '', children, ...props }: ICardGrid) => {
  return (
    <Grid {...props}>
      <CardGroup>
        {title && <CardTitle>{title}</CardTitle>}
        <CardContent>{children}</CardContent>
      </CardGroup>
    </Grid>
  )
}

export default CardGrid
