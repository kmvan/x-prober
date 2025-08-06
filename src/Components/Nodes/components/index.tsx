import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { NodesConstants } from './constants.ts';
import styles from './index.module.scss';
import { Node } from './node.tsx';
import { NodesStore } from './store';
export const Nodes: FC = observer(() => {
  const { pollData } = NodesStore;
  const nodeIds = pollData?.nodesIds ?? [];
  if (!nodeIds.length) {
    return null;
  }
  return (
    <CardItem id={NodesConstants.id} title={gettext('Nodes')}>
      <div className={styles.main}>
        {nodeIds.map((id) => (
          <Node id={id} key={id} />
        ))}
      </div>
    </CardItem>
  );
});
