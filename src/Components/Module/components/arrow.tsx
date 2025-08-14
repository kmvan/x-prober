import { ChevronDown, ChevronUp } from 'lucide-react';
import { observer } from 'mobx-react-lite';
import { type FC, type MouseEvent, useCallback } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import styles from './arrow.module.scss';
import { ModuleStore } from './store.ts';
export const ModuleArrow: FC<{
  isDown: boolean;
  id: string;
}> = observer(({ isDown, id }) => {
  const { disabledMoveUpId, disabledMoveDownId, moveUp, moveDown } =
    ModuleStore;
  const disabled = isDown ? disabledMoveDownId === id : disabledMoveUpId === id;
  const handleMove = useCallback(
    (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      if (isDown) {
        moveDown(id);
        return;
      }
      moveUp(id);
    },
    [isDown, moveDown, moveUp, id]
  );
  return (
    <button
      className={styles.arrow}
      data-disabled={disabled || undefined}
      disabled={disabled}
      onClick={handleMove}
      title={isDown ? gettext('Move down') : gettext('Move up')}
      type="button"
    >
      {isDown ? <ChevronDown /> : <ChevronUp />}
    </button>
  );
});
