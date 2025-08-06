import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { MyInfoConstants } from './constants.ts';
import { MyInfoStore } from './store.ts';

export const MyInfoNav: FC = observer(() => {
  const { pollData } = MyInfoStore;
  if (!pollData) {
    return null;
  }
  return <NavItem id={MyInfoConstants.id} title={gettext('Mine')} />;
});
