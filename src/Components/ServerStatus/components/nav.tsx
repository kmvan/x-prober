import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { ServerStatusConstants } from './constants.ts';
import { ServerStatusStore } from './store.ts';export const ServerStatusNav: FC = observer(() => {
  const { pollData } = ServerStatusStore;
  if (!pollData) {
    return null;
  }
  return <NavItem id={ServerStatusConstants.id} title={gettext('Info')} />;
});
