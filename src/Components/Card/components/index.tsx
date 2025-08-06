import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { PollStore } from '@/Components/Poll/components/store.ts';
import styles from './index.module.scss';
import { CardStore } from './store.ts';
export const Cards: FC = observer(() => {
  const { cardsLength, enabledCards } = CardStore;
  if (!cardsLength) {
    return null;
  }
  return (
    <div className={styles.container}>
      {enabledCards.map(({ id, component: Component }) => {
        return <Component key={id} />;
      })}
    </div>
  );
});
