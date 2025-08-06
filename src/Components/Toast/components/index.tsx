import { observer } from 'mobx-react-lite';
import type { FC, MouseEvent } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Portal } from '@/Components/Utils/components/portal.tsx';
import styles from './index.module.scss';
import { ToastStore } from './store.ts';
export const Toast: FC = observer(() => {
  const { isOpen, msg, close } = ToastStore;
  const handleClose = (e: MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();
    e.stopPropagation();
    close();
  };
  if (!isOpen) {
    return null;
  }
  return (
    <Portal>
      <button
        className={styles.main}
        onClick={handleClose}
        title={gettext('Click to close')}
        type="button"
      >
        {msg}
      </button>
    </Portal>
  );
});
