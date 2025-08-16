import type { CSSProperties, FC, HTMLProps } from 'react';
import styles from './multi.module.scss';

interface UiMultiColContainerProps extends HTMLProps<HTMLDivElement> {
  minWidth?: number;
}
export const UiMultiColContainer: FC<UiMultiColContainerProps> = ({
  minWidth = 16,
  ...props
}) => {
  const style = { '--min-width': `${minWidth}rem` } as CSSProperties;
  return <div className={styles.main} style={style} {...props} />;
};
