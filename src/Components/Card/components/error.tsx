import type { FC, HTMLAttributes } from 'react';
import styles from './error.module.scss';
export const CardError: FC<HTMLAttributes<HTMLDivElement>> = ({ children }) => (
  <div className={styles.main} role="alert">
    {children}
  </div>
);
