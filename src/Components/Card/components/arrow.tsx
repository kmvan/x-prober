import { ChevronDown, ChevronUp } from 'lucide-react';
import { type FC, type MouseEvent, useCallback } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import styles from './arrow.module.scss';
export const CardArrow: FC<{
  isDown: boolean;
  disabled: boolean;
  id: string;
  handleClick: (id: string) => void;
}> = ({ isDown, disabled, id, handleClick }) => {
  const handleMove = useCallback(
    (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      handleClick(id);
    },
    [handleClick, id]
  );
  return (
    <button
      className={styles.arrow}
      data-disabled={disabled || undefined}
      onClick={handleMove}
      title={isDown ? gettext('Move down') : gettext('Move up')}
      type="button"
    >
      {isDown ? <ChevronDown /> : <ChevronUp />}
    </button>
  );
};
