import type { FC, HTMLProps, ReactNode } from 'react';
import styles from './index.module.scss';
export interface UiRubyProps extends HTMLProps<HTMLElement> {
  isResult?: boolean;
  ruby: ReactNode;
  rt: string;
}
export const UiRuby: FC<UiRubyProps> = ({
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
