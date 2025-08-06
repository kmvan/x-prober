import type { FC } from 'react';
import styles from './index.module.scss';

export const NavItem: FC<{
  id: string;
  title: string;
}> = ({ id, title }) => {
  return (
    <a className={styles.link} href={`#${id}`} key={id}>
      <span className={styles.linkTitle}>{title}</span>
    </a>
  );
};
