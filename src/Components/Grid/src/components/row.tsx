import React from 'react'
import styled from 'styled-components'
import { GUTTER } from '~components/Config/src'

const Div = styled.div`
  display: flex;
  flex-wrap: wrap;
  margin-left: calc(-${GUTTER} / 2);
  margin-right: calc(-${GUTTER} / 2);
`

const Row = props => {
  return <Div {...props} />
}

export default Row
