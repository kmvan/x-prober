import React from 'react'
import styled from 'styled-components'
const StyledMultiItemContainer = styled.div`
  display: flex;
  flex-wrap: wrap;
  margin-bottom: -0.2rem;
`
export const MultiItemContainer = (props) => (
  <StyledMultiItemContainer {...props} />
)
