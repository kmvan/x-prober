import React from 'react'
import styled from 'styled-components'

const StyledCardLink = styled.a`
  ::before {
    content: '👆 ';
  }
`

const CardLink = ({ children, ...props }) => {
  return (
    <StyledCardLink target='_blank' {...props}>
      {children}
    </StyledCardLink>
  )
}

export default CardLink
