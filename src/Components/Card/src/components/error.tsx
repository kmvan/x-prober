import React, { FC, HTMLAttributes } from 'react'
import styled from 'styled-components'
import { GUTTER } from '../../../Config/src'
const StyledCardError = styled.div`
  padding: ${GUTTER};
`
type CardErrorProps = HTMLAttributes<HTMLDivElement>
export const CardError: FC<CardErrorProps> = (props) => (
  <StyledCardError {...props} />
)
