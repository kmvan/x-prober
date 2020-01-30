import React, { ReactNode } from 'react'
import styled from 'styled-components'
import { rgba } from 'polished'
import { COLOR_DARK } from '~components/Config/src'

interface StyledRubyProps {
  isResult?: boolean
}

export interface CardRubyProps extends StyledRubyProps {
  ruby: ReactNode
  rt: string
}

const StyledRuby = styled.ruby<StyledRubyProps>`
  background: ${rgba(COLOR_DARK, 0.05)};
  rp {
  }
  rt {
    font-size: 0.75rem;
    opacity: 0.5;
  }
  font-weight: ${p => (p.isResult ? 'bold' : 'unset')};
`

const CardRuby = ({ ruby, rt, isResult = false }: CardRubyProps) => {
  return (
    <StyledRuby isResult={isResult}>
      {ruby}
      <rp>(</rp>
      <rt>{rt}</rt>
      <rp>)</rp>
    </StyledRuby>
  )
}

export default CardRuby
