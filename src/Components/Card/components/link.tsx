import type { AnchorHTMLAttributes, FC } from 'react';
import styles from './link.module.scss';
export const CardLink: FC<AnchorHTMLAttributes<HTMLAnchorElement>> = (
  props
) => <a className={styles.main} target="_blank" {...props} />;
