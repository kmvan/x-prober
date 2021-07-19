import React, { FC } from 'react'
import styled from 'styled-components'
import { BORDER_RADIUS, GUTTER } from '../../../Config/src'

const StyledCardDes = styled.div`
  padding: calc(${GUTTER} / 2) ${GUTTER};
  background-color: ${({ theme }) => theme['card.des.bg']};
  color: ${({ theme }) => theme['card.des.fg']};
  border-radius: ${BORDER_RADIUS};
  margin-bottom: ${GUTTER};
`
export const CardDes: FC = (props) => {
  return <StyledCardDes {...props} />
}
