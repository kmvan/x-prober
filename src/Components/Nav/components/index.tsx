import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { CardStore } from '@/Components/Card/components/store.ts';
import styles from './index.module.scss';
export const Nav: FC = observer(() => {
  const { enabledCards } = CardStore;
  // const { activeIndex } = NavStore;
  const items = enabledCards.map(({ id, nav: Component, enabled = true }) => {
    if (!enabled) {
      return null;
    }
    return <Component key={id} />;
  });
  // .filter((n) => n) as ReactElement[];
  return (
    <div className={styles.main}>
      {items}
      {/* <ElevatorNav activeIndex={activeIndex}>{items}</ElevatorNav> */}
    </div>
  );
});
