import React from 'react'
import styled from 'styled-components'
import { GUTTER } from '~components/Config/src'

const Div = styled.div`
  padding: ${GUTTER};
`

const CardError = props => <Div {...props} />

export default CardError
