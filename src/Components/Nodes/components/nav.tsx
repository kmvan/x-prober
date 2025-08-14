import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { NodesConstants } from './constants.ts';
import { NodesStore } from './store.ts';export const NodesNav: FC = observer(() => {
  const { pollData } = NodesStore;
  const nodeIds = pollData?.nodesIds ?? [];
  if (!nodeIds.length) {
    return null;
  }
  return <NavItem id={NodesConstants.id} title={gettext('Nodes')} />;
});
