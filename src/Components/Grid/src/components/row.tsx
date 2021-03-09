import { GUTTER } from '@/Config/src'
import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
const StyledRow = styled.div`
  display: flex;
  flex-wrap: wrap;
  margin-left: calc(-${GUTTER} / 2);
  margin-right: calc(-${GUTTER} / 2);
`
interface RowProps extends HTMLAttributes<HTMLDivElement> {}
export const Row = (props: RowProps) => {
  return <StyledRow {...props} />
}
