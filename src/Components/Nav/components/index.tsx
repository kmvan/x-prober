import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { ModuleStore } from '@/Components/Module/components/store';
import styles from './index.module.scss';
export const Nav: FC = observer(() => {
  const { availableModules } = ModuleStore;
  // const { activeIndex } = NavStore;
  const items = availableModules.map(({ id, nav: Component }) => {
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
