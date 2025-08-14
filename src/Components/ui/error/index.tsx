import type { FC, HTMLAttributes } from 'react';
import styles from './index.module.scss';
export const UiError: FC<HTMLAttributes<HTMLDivElement>> = ({ children }) => (
  <div className={styles.main} role="alert">
    {children}
  </div>
);
