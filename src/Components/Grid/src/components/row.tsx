import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
import { GUTTER } from '~components/Config/src'

const StyledRow = styled.div`
  display: flex;
  flex-wrap: wrap;
  margin-left: calc(-${GUTTER} / 2);
  margin-right: calc(-${GUTTER} / 2);
`

interface IRow extends HTMLAttributes<HTMLDivElement> {}

const Row = (props: IRow) => {
  return <StyledRow {...props} />
}

export default Row
