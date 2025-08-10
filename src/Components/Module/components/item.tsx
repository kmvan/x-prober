import type { FC, ReactNode } from 'react';
import { ModuleArrow } from '@/Components/Module/components/arrow.tsx';
import styles from './item.module.scss';

const ModuleItemTitle: FC<{
  id: string;
  title: string;
}> = ({ id, title }) => {
  return (
    <h2 className={styles.header}>
      <ModuleArrow id={id} isDown={false} />
      <span className={styles.title}>{title}</span>
      <ModuleArrow id={id} isDown />
    </h2>
  );
};
export const ModuleItem: FC<{
  id: string;
  title: string;
  children: ReactNode;
}> = ({ id, title, children, ...props }) => {
  return (
    <div className={styles.main} id={id} {...props}>
      <ModuleItemTitle id={id} title={title} />
      <div className={styles.body}>{children}</div>
    </div>
  );
};
