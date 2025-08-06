import type { FC } from 'react';
import styles from './index.module.scss';
export const Placeholder: FC<{ height?: number }> = ({ height = 5 }) => {
  return <div className={styles.main} style={{ height: `${height}rem` }} />;
};
