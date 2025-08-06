import type { FC, HTMLProps } from 'react';
import styles from './single-col.module.scss';
export const CardSingleColContainer: FC<HTMLProps<HTMLDivElement>> = (
  props
) => <div className={styles.main} {...props} />;
