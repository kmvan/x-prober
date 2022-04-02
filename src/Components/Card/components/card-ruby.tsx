import { rgba } from 'polished'
import { FC, HTMLAttributes, ReactNode } from 'react'
import styled from 'styled-components'
import { gettext } from '../../Language'
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
export const CardRuby: FC<CardRubyProps> = ({
  ruby,
  rt,
  isResult = false,
  ...props
}) => (
  <StyledRuby isResult={isResult} {...props} title={gettext('Copy marks')}>
    {ruby}
    <rp>(</rp>
    <rt>{rt}</rt>
    <rp>)</rp>
  </StyledRuby>
)
