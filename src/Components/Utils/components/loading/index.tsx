import type { FC, HTMLProps } from 'react';
import styles from './index.module.scss';
export const Loading: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.main}>
    <div className={styles.text} {...props} />
  </div>
);
