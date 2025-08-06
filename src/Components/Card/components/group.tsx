import type { FC, ReactNode } from 'react';
import styles from './group.module.scss';
export interface CardGroupProps {
  label?: ReactNode;
  children: ReactNode;
  title?: string;
}
export const CardGroup: FC<CardGroupProps> = ({
  label = '',
  title = '',
  children,
}) => (
  <div className={styles.main}>
    {Boolean(label) && (
      <div className={styles.label} title={title}>
        {label}
      </div>
    )}
    <div className={styles.content}>{children}</div>
  </div>
);
