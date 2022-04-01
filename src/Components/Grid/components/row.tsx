import React, { FC, HTMLAttributes } from 'react'
import styled from 'styled-components'
import { GUTTER } from '../../Config'
const StyledRow = styled.div`
  display: flex;
  flex-wrap: wrap;
  margin-left: calc(-${GUTTER} / 2);
  margin-right: calc(-${GUTTER} / 2);
`
type RowProps = HTMLAttributes<HTMLDivElement>
export const Row: FC<RowProps> = (props) => <StyledRow {...props} />
