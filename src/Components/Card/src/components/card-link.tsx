import React, { AnchorHTMLAttributes, FC } from 'react'
import styled from 'styled-components'

const StyledCardLink = styled.a`
  ::before {
    content: '👆 ';
  }
`
export const CardLink: FC<AnchorHTMLAttributes<HTMLAnchorElement>> = (
  props
) => {
  return <StyledCardLink target='_blank' {...props} />
}
