import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { ServerInfoConstants } from './constants.ts';
import { ServerInfoStore } from './store.ts';

export const ServerInfoNav: FC = observer(() => {
  const { pollData } = ServerInfoStore;
  if (!pollData) {
    return null;
  }
  return <NavItem id={ServerInfoConstants.id} title={gettext('Info')} />;
});
