import type { FC, ReactNode } from 'react';
import styles from './index.module.scss';
export const EnableStatus: FC<{
  isEnable: boolean;
  text?: ReactNode;
}> = ({ isEnable, text = '' }) => (
  <div
    className={styles.main}
    data-error={!isEnable || undefined}
    data-icon={!text || undefined}
    data-ok={isEnable || undefined}
  >
    {text}
  </div>
);
