import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
import { GUTTER } from '@/Config/src'
const StyledCardError = styled.div`
  padding: ${GUTTER};
`
interface CardErrorProps extends HTMLAttributes<HTMLDivElement> {}
const CardError = (props: CardErrorProps) => <StyledCardError {...props} />
export default CardError
