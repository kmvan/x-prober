import { observer } from 'mobx-react-lite';
import type { FC, ReactNode } from 'react';
import { CardArrow } from './arrow.tsx';
import styles from './item.module.scss';
import { CardStore } from './store.ts';

const CardItemTitle: FC<{
  id: string;
  title: string;
}> = observer(({ id, title }) => {
  const { disabledMoveUpId, disabledMoveDownId, moveCardDown, moveCardUp } =
    CardStore;
  return (
    <h2 className={styles.header}>
      <CardArrow
        disabled={id === disabledMoveUpId}
        handleClick={moveCardUp}
        id={id}
        isDown={false}
      />
      <span className={styles.title}>{title}</span>
      <CardArrow
        disabled={id === disabledMoveDownId}
        handleClick={moveCardDown}
        id={id}
        isDown
      />
    </h2>
  );
});
export const CardItem: FC<{
  id: string;
  title: string;
  children: ReactNode;
}> = ({ id, title, children, ...props }) => {
  return (
    <div className={styles.main} id={id} {...props}>
      <CardItemTitle id={id} title={title} />
      <div className={styles.body}>{children}</div>
    </div>
  );
};
