import { GUTTER } from '@/Config/src'
import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
const StyledCardError = styled.div`
  padding: ${GUTTER};
`
interface CardErrorProps extends HTMLAttributes<HTMLDivElement> {}
export const CardError = (props: CardErrorProps) => (
  <StyledCardError {...props} />
)
