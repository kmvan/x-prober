import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { DiskUsageConstants } from './constants.ts';
import { DiskUsageStore } from './store.ts';

export const DiskUsageNav: FC = observer(() => {
  const { pollData } = DiskUsageStore;
  const items = pollData?.items ?? [];
  if (!items.length) {
    return null;
  }
  return <NavItem id={DiskUsageConstants.id} title={gettext('Disk')} />;
});
