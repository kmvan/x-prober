import type { FC, HTMLProps } from 'react';
import styles from './single.module.scss';
export const UiSingleColContainer: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.main} {...props} />
);
