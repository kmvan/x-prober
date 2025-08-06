import type { AnchorHTMLAttributes, ButtonHTMLAttributes, FC } from 'react';
import styles from './link.module.scss';
export const HeaderLink: FC<AnchorHTMLAttributes<HTMLAnchorElement>> = (
  props
) => <a className={styles.main} {...props} />;
export const HeaderButton: FC<ButtonHTMLAttributes<HTMLButtonElement>> = (
  props
) => <button className={styles.main} {...props} />;
