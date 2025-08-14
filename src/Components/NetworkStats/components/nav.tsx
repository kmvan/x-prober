import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { NetworkStatsConstants } from './constants.ts';
import { NetworkStatsStore } from './store.ts';export const NetworkStatsNav: FC = observer(() => {
  const { networksCount } = NetworkStatsStore;
  if (!networksCount) {
    return null;
  }
  return <NavItem id={NetworkStatsConstants.id} title={gettext('Network')} />;
});
