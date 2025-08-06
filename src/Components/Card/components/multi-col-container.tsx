import type { FC, HTMLProps } from 'react';
import styles from './multi-col.module.scss';
export const CardMultiColContainer: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.main} {...props} />
);
