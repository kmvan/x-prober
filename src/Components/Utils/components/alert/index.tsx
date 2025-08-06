import type { FC, ReactNode } from 'react';
import styles from './index.module.scss';
export interface AlertProps {
  isSuccess: boolean;
  msg?: ReactNode;
}
export const Alert: FC<AlertProps> = ({ isSuccess, msg = '' }) => (
  <div
    className={styles.main}
    data-error={!isSuccess || undefined}
    data-icon={!msg || undefined}
    data-ok={isSuccess || undefined}
  >
    {msg}
  </div>
);
