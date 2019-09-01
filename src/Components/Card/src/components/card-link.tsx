import React from 'react'
import styled from 'styled-components'

const A = styled.a`
  ::before {
    content: '👆 ';
  }
`

const CardLink = ({ children, ...props }) => {
  return (
    <A target='_blank' {...props}>
      {children}
    </A>
  )
}

export default CardLink
