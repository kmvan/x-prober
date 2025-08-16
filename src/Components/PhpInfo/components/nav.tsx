import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { PhpInfoConstants } from './constants.ts';
import { PhpInfoStore } from './store.ts';
export const PhpInfoNav: FC = observer(() => {
  const { pollData } = PhpInfoStore;
  if (!pollData) {
    return null;
  }
  return <NavItem id={PhpInfoConstants.id} title={gettext('PHP Info')} />;
});
