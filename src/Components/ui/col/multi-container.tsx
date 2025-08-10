import type { FC, HTMLProps } from 'react';
import styles from './multi.module.scss';
export const UiMultiColContainer: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.main} {...props} />
);
