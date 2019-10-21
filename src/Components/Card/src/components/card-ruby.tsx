import React, { ReactNode } from 'react'
import styled from 'styled-components'

interface IRuby {
  isResult?: boolean
}

export interface ICardRuby extends IRuby {
  ruby: ReactNode
  rt: string
}

const StyledRuby = styled.ruby<IRuby>`
  background: rgba(0, 0, 0, 0.05);
  rp {
  }
  rt {
    font-size: 0.75rem;
    opacity: 0.5;
  }
  font-weight: ${p => (p.isResult ? 'bold' : 'unset')};
`

const CardRuby = ({ ruby, rt, isResult = false }: ICardRuby) => {
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
