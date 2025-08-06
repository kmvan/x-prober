import type { FC, HTMLProps, ReactNode } from 'react';
import styles from './ruby.module.scss';
export interface CardRubyProps extends HTMLProps<HTMLElement> {
  isResult?: boolean;
  ruby: ReactNode;
  rt: string;
}
export const CardRuby: FC<CardRubyProps> = ({
  ruby,
  rt,
  isResult = false,
  ...props
}) => (
  <ruby
    className={styles.main}
    data-is-result={isResult || undefined}
    {...props}
  >
    {ruby}
    <rp>(</rp>
    <rt>{rt}</rt>
    <rp>)</rp>
  </ruby>
);
