import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
import { GUTTER } from '~components/Config/src'

const StyledCardError = styled.div`
  padding: ${GUTTER};
`

interface ICardError extends HTMLAttributes<HTMLDivElement> {}

const CardError = (props: ICardError) => <StyledCardError {...props} />

export default CardError
