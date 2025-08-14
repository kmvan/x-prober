import { observer } from 'mobx-react-lite';
import type { FC, MouseEvent } from 'react';
import { NavStore } from '@/Components/Nav/components/store.ts';
import styles from './bar.module.scss';
export const HeaderBar: FC = observer(() => {
  const { isOpen, setIsOpen } = NavStore;
  const handleToggleMenu = (e: MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();
    e.stopPropagation();
    setIsOpen(!isOpen);
  };
  return (
    <button
      className={styles.main}
      data-active={isOpen || undefined}
      onClick={handleToggleMenu}
      type="button"
    >
      <span className={styles.line} />
      <span className={styles.line} />
      <span className={styles.line} />
    </button>
  );
});
