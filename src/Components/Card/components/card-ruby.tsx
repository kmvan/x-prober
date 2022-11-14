import { FC, HTMLAttributes, ReactNode } from 'react'
import { gettext } from '../../Language'
interface StyledRubyProps extends HTMLAttributes<HTMLElement> {
  isResult?: boolean
}
export interface CardRubyProps extends StyledRubyProps {
  ruby: ReactNode
  rt: string
}
export const CardRuby: FC<CardRubyProps> = ({
  ruby,
  rt,
  isResult = false,
  ...props
}) => (
  <ruby
    data-is-result={isResult || undefined}
    title={gettext('Copy marks')}
    {...props}
  >
    {ruby}
    <rp>(</rp>
    <rt>{rt}</rt>
    <rp>)</rp>
  </ruby>
)
