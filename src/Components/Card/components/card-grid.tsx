import { FC, ReactNode } from 'react'
import styled from 'styled-components'
import { GUTTER } from '../../Config'
import { Grid, GridProps } from '../../Grid/components/grid'
import { device } from '../../Style/components/devices'
export interface CardGridProps extends GridProps {
  name?: ReactNode
  children: ReactNode
  title?: string
}
const StyledCardGroup = styled.div`
  display: flex;
  width: 100%;
  align-items: center;
  border-bottom: 1px dashed ${({ theme }) => theme['card.border']};
  :hover {
    background: ${({ theme }) => theme['card.hover.bg']};
  }
`
const StyledCardTitle = styled.div`
  word-break: normal;
  padding: calc(${GUTTER} / 2) 0;
  flex: 0 0 8rem;
  color: ${({ theme }) => theme['card.title.fg']};
  @media ${device('tablet')} {
    flex: 0 0 12rem;
  }
`
const StyledCardContent = styled.div`
  flex-grow: 1;
  padding: calc(${GUTTER} / 2) 0;
`
export const CardGrid: FC<CardGridProps> = ({
  name = '',
  title = '',
  children,
  ...props
}) => (
  <Grid {...props}>
    <StyledCardGroup>
      {Boolean(name) && <StyledCardTitle title={title}>{name}</StyledCardTitle>}
      <StyledCardContent>{children}</StyledCardContent>
    </StyledCardGroup>
  </Grid>
)
