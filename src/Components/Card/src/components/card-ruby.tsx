import { gettext } from '@/Language/src'
import { rgba } from 'polished'
import React, { HTMLAttributes, ReactNode } from 'react'
import styled from 'styled-components'
interface StyledRubyProps extends HTMLAttributes<HTMLElement> {
  isResult?: boolean
}
export interface CardRubyProps extends StyledRubyProps {
  ruby: ReactNode
  rt: string
}
const StyledRuby = styled.ruby<StyledRubyProps>`
  background: ${({ theme }) => rgba(theme['benchmark.ruby.bg'], 0.05)};
  cursor: pointer;
  :hover {
    text-decoration: underline;
  }
  rp {
  }
  rt {
    font-size: 0.75rem;
    opacity: 0.5;
  }
  font-weight: ${(p) => (p.isResult ? 'bold' : 'unset')};
`
export const CardRuby = ({
  ruby,
  rt,
  isResult = false,
  ...props
}: CardRubyProps) => {
  return (
    <StyledRuby isResult={isResult} {...props} title={gettext('Copy marks')}>
      {ruby}
      <rp>(</rp>
      <rt>{rt}</rt>
      <rp>)</rp>
    </StyledRuby>
  )
}
