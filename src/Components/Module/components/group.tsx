import type { CSSProperties, FC, ReactNode } from 'react';
import styles from './group.module.scss';
export const ModuleGroup: FC<{
  label?: ReactNode;
  children: ReactNode;
  title?: string;
  minWidth?: number;
  maxWidth?: number;
}> = ({ label = '', title = '', minWidth = 4, maxWidth = 8, children }) => {
  const style = {
    '--min-width': `${minWidth}rem`,
    '--max-width': `${maxWidth}rem`,
  } as CSSProperties;
  return (
    <div className={styles.main} style={style}>
      {Boolean(label) && (
        <div className={styles.label} title={title}>
          {label}
        </div>
      )}
      <div className={styles.content}>{children}</div>
    </div>
  );
};
